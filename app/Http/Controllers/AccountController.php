<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountEventLog;
use App\Models\EmailLog;
use App\Models\ErrorMessage;
use App\Models\User;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AccountController extends Controller
{
    /**
     * Chart of Accounts – list with search, filter, pagination.
     */
    public function index(Request $request)
    {
        $query = Account::with('creator')
            ->search($request->input('search'))
            ->filterCategory($request->input('category'))
            ->filterSubcategory($request->input('subcategory'))
            ->filterStatement($request->input('statement'))
            ->filterStatus($request->input('status'))
            ->orderBy('order')
            ->orderBy('account_number');

        // Date filter (for accounts added within a range)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Amount filter
        if ($request->filled('min_amount')) {
            $query->where('balance', '>=', $request->input('min_amount'));
        }
        if ($request->filled('max_amount')) {
            $query->where('balance', '<=', $request->input('max_amount'));
        }

        $accounts = $query->paginate(20)->withQueryString();

        // Gather unique subcategories for filter dropdown
        $subcategories = Account::select('account_subcategory')
            ->distinct()
            ->orderBy('account_subcategory')
            ->pluck('account_subcategory');

        return view('accounts.index', compact('accounts', 'subcategories'));
    }

    /**
     * Show the create account form (admin only).
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Store a new account (admin only).
     */
    public function store(StoreAccountRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $account = Account::create($data);

        // Log event with after image (no before image for create)
        AccountEventLog::create([
            'account_id' => $account->id,
            'user_id' => auth()->id(),
            'event_type' => 'created',
            'before_image' => null,
            'after_image' => $account->toSnapshot(),
        ]);

        return redirect()
            ->route('accounts.index')
            ->with('status', "Account \"{$account->account_name}\" (#{$account->account_number}) created successfully.");
    }

    /**
     * Show individual account detail.
     */
    public function show(Account $account)
    {
        $account->load('creator', 'eventLogs.user');

        $recipients = User::whereIn('role', ['manager', 'admin'])
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email', 'role']);

        return view('accounts.show', compact('account', 'recipients'));
    }

    /**
     * Email a manager/admin from the account page. Uses the existing EmailLog +
     * Mail::raw pattern from Admin\EmailController to keep the audit trail consistent.
     */
    public function email(Request $request, Account $account)
    {
        $validated = $request->validate([
            'recipient_email' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $recipient = User::where('email', $validated['recipient_email'])
            ->whereIn('role', ['manager', 'admin'])
            ->where('status', 'active')
            ->first();

        if (!$recipient) {
            $msg = ErrorMessage::getByCode('EMAIL_RECIPIENT_INVALID')
                ?? 'Recipient must be an active manager or administrator.';
            return back()->withErrors(['recipient_email' => $msg])->withInput();
        }

        EmailLog::create([
            'user_id' => $recipient->id,
            'recipient' => $recipient->email,
            'subject' => $validated['subject'],
            'body' => "[Re: Account {$account->account_number} — {$account->account_name}]\n\n" . $validated['body'],
            'sent_by' => auth()->id(),
            'sent_at' => now(),
        ]);

        try {
            Mail::raw($validated['body'], function ($message) use ($recipient, $validated) {
                $message->to($recipient->email, $recipient->full_name)
                    ->subject($validated['subject']);
            });
        } catch (\Exception $e) {
            Log::error('Account email failed: ' . $e->getMessage());
            return redirect()->route('accounts.show', $account)
                ->with('error', "Email logged but delivery failed: {$e->getMessage()}");
        }

        return redirect()->route('accounts.show', $account)
            ->with('status', "Email sent to {$recipient->full_name} ({$recipient->email}).");
    }

    /**
     * Show the edit form (admin only).
     */
    public function edit(Account $account)
    {
        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the account (admin only).
     */
    public function update(UpdateAccountRequest $request, Account $account)
    {
        $beforeImage = $account->toSnapshot();

        $account->update($request->validated());

        $account->refresh();

        // Log event with before and after images
        AccountEventLog::create([
            'account_id' => $account->id,
            'user_id' => auth()->id(),
            'event_type' => 'updated',
            'before_image' => $beforeImage,
            'after_image' => $account->toSnapshot(),
        ]);

        return redirect()
            ->route('accounts.show', $account)
            ->with('status', "Account \"{$account->account_name}\" updated successfully.");
    }

    /**
     * Deactivate an account (admin only).
     * Accounts with balance > 0 cannot be deactivated.
     */
    public function deactivate(Account $account)
    {
        if ((float) $account->balance != 0) {
            $msg = ErrorMessage::getByCode('ACCT_DEACTIVATE_BALANCE')
                ?? 'Accounts with a balance greater than zero cannot be deactivated.';

            return redirect()
                ->route('accounts.show', $account)
                ->with('error', $msg);
        }

        $beforeImage = $account->toSnapshot();

        $account->update(['is_active' => false]);

        AccountEventLog::create([
            'account_id' => $account->id,
            'user_id' => auth()->id(),
            'event_type' => 'deactivated',
            'before_image' => $beforeImage,
            'after_image' => $account->fresh()->toSnapshot(),
        ]);

        return redirect()
            ->route('accounts.show', $account)
            ->with('status', "Account \"{$account->account_name}\" has been deactivated.");
    }

    /**
     * Show the event log for an account.
     */
    public function eventLog(Account $account)
    {
        $events = $account->eventLogs()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('accounts.event-log', compact('account', 'events'));
    }
}
