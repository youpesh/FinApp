<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\FinancialReport;
use App\Models\User;
use App\Services\FinancialReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReportController extends Controller
{
    public function __construct(private FinancialReportService $service)
    {
    }

    public function index(Request $request)
    {
        $snapshots = FinancialReport::with('generator')
            ->orderByDesc('generated_at')
            ->paginate(15);

        return view('reports.index', [
            'snapshots' => $snapshots,
            'types' => FinancialReport::TYPES,
        ]);
    }

    public function trialBalance(Request $request)
    {
        $asOf = $this->parseDate($request->input('as_of'), now());
        $data = $this->service->trialBalance($asOf);

        return view('reports.trial-balance', [
            'data' => $data,
            'asOf' => $asOf,
            'recipients' => $this->managerRecipients(),
        ]);
    }

    public function incomeStatement(Request $request)
    {
        [$from, $to] = $this->parseRange($request);
        $data = $this->service->incomeStatement($from, $to);

        return view('reports.income-statement', [
            'data' => $data,
            'from' => $from,
            'to' => $to,
            'recipients' => $this->managerRecipients(),
        ]);
    }

    public function balanceSheet(Request $request)
    {
        $asOf = $this->parseDate($request->input('as_of'), now());
        $data = $this->service->balanceSheet($asOf);

        return view('reports.balance-sheet', [
            'data' => $data,
            'asOf' => $asOf,
            'recipients' => $this->managerRecipients(),
        ]);
    }

    public function retainedEarnings(Request $request)
    {
        [$from, $to] = $this->parseRange($request);
        $data = $this->service->retainedEarnings($from, $to);

        return view('reports.retained-earnings', [
            'data' => $data,
            'from' => $from,
            'to' => $to,
            'recipients' => $this->managerRecipients(),
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(FinancialReport::TYPES)),
            'as_of' => 'nullable|date',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $report = $this->buildReportRecord($validated);

        return redirect()
            ->route('reports.snapshot.show', $report)
            ->with('success', 'Report saved.');
    }

    public function email(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(FinancialReport::TYPES)),
            'recipient_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
            'as_of' => 'nullable|date',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        // Recipient must be a manager or admin
        $recipient = User::where('email', $validated['recipient_email'])
            ->whereIn('role', ['manager', 'admin'])
            ->where('status', 'active')
            ->first();

        if (!$recipient) {
            return back()->withErrors(['recipient_email' => 'Recipient must be an active manager or admin.']);
        }

        // Build PDF
        [$pdf, $filename] = $this->buildPdf($validated);

        // Log
        EmailLog::create([
            'user_id' => $recipient->id,
            'recipient' => $recipient->email,
            'subject' => $validated['subject'],
            'body' => $validated['body'] . "\n\n[Attachment: {$filename}]",
            'sent_by' => auth()->id(),
            'sent_at' => now(),
        ]);

        try {
            Mail::send([], [], function ($message) use ($recipient, $validated, $pdf, $filename) {
                $message->to($recipient->email, $recipient->full_name)
                    ->subject($validated['subject'])
                    ->text($validated['body'])
                    ->attachData($pdf->output(), $filename, ['mime' => 'application/pdf']);
            });
        } catch (\Exception $e) {
            Log::error('Report email failed: ' . $e->getMessage());
            return back()->with('error', "Report logged but delivery failed: {$e->getMessage()}");
        }

        return back()->with('success', "Report emailed to {$recipient->full_name} ({$recipient->email}).");
    }

    public function pdf(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, FinancialReport::TYPES), 404);

        $params = $request->only(['as_of', 'date_from', 'date_to']);
        $params['type'] = $type;

        [$pdf, $filename] = $this->buildPdf($params);

        return $pdf->download($filename);
    }

    public function showSnapshot(FinancialReport $financialReport)
    {
        return view('reports.snapshot', ['snapshot' => $financialReport]);
    }

    // ── helpers ──────────────────────────────────────────────────────

    private function parseDate(?string $input, ?Carbon $default = null): Carbon
    {
        if (!$input) {
            return $default ?? now();
        }
        return Carbon::parse($input)->endOfDay();
    }

    private function parseRange(Request $request): array
    {
        $from = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : now()->startOfYear();
        $to = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : now();
        return [$from, $to];
    }

    private function managerRecipients()
    {
        return User::whereIn('role', ['manager', 'admin'])
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email', 'role']);
    }

    /**
     * Build and persist a FinancialReport snapshot from the request params.
     */
    private function buildReportRecord(array $params): FinancialReport
    {
        [$data, $parameters, $title] = $this->buildReportData($params);

        return FinancialReport::create([
            'type' => $params['type'],
            'title' => $title,
            'parameters' => $parameters,
            'payload' => $data,
            'generated_by' => auth()->id(),
            'generated_at' => now(),
        ]);
    }

    /**
     * Build a dompdf instance from a params payload (not persisted).
     * @return array{0: \Barryvdh\DomPDF\PDF, 1: string}
     */
    private function buildPdf(array $params): array
    {
        [$data, $parameters, $title] = $this->buildReportData($params);

        $viewName = 'reports.pdf.' . str_replace('_', '-', $params['type']);
        $pdf = Pdf::loadView($viewName, [
            'data' => $data,
            'title' => $title,
            'parameters' => $parameters,
        ]);

        $filename = $params['type'] . '-' . now()->format('Ymd_His') . '.pdf';
        return [$pdf, $filename];
    }

    /**
     * Compute report data + prepared params + title based on type.
     * @return array{0: array, 1: array, 2: string}
     */
    private function buildReportData(array $params): array
    {
        $type = $params['type'];
        $title = FinancialReport::TYPES[$type];

        switch ($type) {
            case 'trial_balance':
                $asOf = $this->parseDate($params['as_of'] ?? null, now());
                $data = $this->service->trialBalance($asOf);
                $parameters = ['as_of' => $asOf->toDateString()];
                $title .= ' as of ' . $asOf->toFormattedDateString();
                break;
            case 'balance_sheet':
                $asOf = $this->parseDate($params['as_of'] ?? null, now());
                $data = $this->service->balanceSheet($asOf);
                $parameters = ['as_of' => $asOf->toDateString()];
                $title .= ' as of ' . $asOf->toFormattedDateString();
                break;
            case 'income_statement':
                $from = !empty($params['date_from']) ? Carbon::parse($params['date_from']) : now()->startOfYear();
                $to = !empty($params['date_to']) ? Carbon::parse($params['date_to']) : now();
                $data = $this->service->incomeStatement($from, $to);
                $parameters = ['date_from' => $from->toDateString(), 'date_to' => $to->toDateString()];
                $title .= " ({$from->toFormattedDateString()} – {$to->toFormattedDateString()})";
                break;
            case 'retained_earnings':
                $from = !empty($params['date_from']) ? Carbon::parse($params['date_from']) : now()->startOfYear();
                $to = !empty($params['date_to']) ? Carbon::parse($params['date_to']) : now();
                $data = $this->service->retainedEarnings($from, $to);
                $parameters = ['date_from' => $from->toDateString(), 'date_to' => $to->toDateString()];
                $title .= " ({$from->toFormattedDateString()} – {$to->toFormattedDateString()})";
                break;
            default:
                abort(404);
        }

        return [$data, $parameters, $title];
    }
}
