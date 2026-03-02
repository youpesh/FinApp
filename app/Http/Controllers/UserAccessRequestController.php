<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccessRequest;
use App\Services\PasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserAccessRequestController extends Controller
{
    /**
     * Show the form for requesting access.
     */
    public function create()
    {
        return view('auth.request-access');
    }

    /**
     * Store a new access request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:user_access_requests,email,NULL,id,status,pending'],
            'address' => ['nullable', 'string', 'max:255'],
            'dob' => ['nullable', 'date'],
        ]);

        UserAccessRequest::create($validated);

        return redirect()->route('login')->with('status', 'Your access request has been submitted and is pending administrator approval.');
    }

    /**
     * Display a listing of the requests (Admin only).
     */
    public function index()
    {
        $requests = UserAccessRequest::where('status', 'pending')->paginate(15);
        return view('admin.requests.index', compact('requests'));
    }

    /**
     * Approve the request.
     */
    public function approve(Request $request, UserAccessRequest $accessRequest, PasswordService $passwordService)
    {
        if ($accessRequest->status !== 'pending') {
            return back()->with('error', 'Request has already been processed.');
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'manager', 'accountant'])],
        ]);

        // Generate username
        $username = $passwordService->generateUsername($accessRequest->first_name, $accessRequest->last_name);

        // Generate a temporary password (or we could trigger a password reset email instead)
        $temporaryPassword = Str::random(12) . 'A1!'; // Ensuring it passes the strong password rule visually

        $user = User::create([
            'first_name' => $accessRequest->first_name,
            'last_name' => $accessRequest->last_name,
            'username' => $username,
            'email' => $accessRequest->email,
            'password' => $temporaryPassword,
            'address' => $accessRequest->address,
            'dob' => $accessRequest->dob,
            'role' => $validated['role'],
            'status' => 'active', // Active immediately upon approval
            'password_expires_at' => now()->subDay(), // Force password change on first login
            'created_by' => auth()->id(),
        ]);

        $passwordService->saveToHistory($user, $user->password);

        $accessRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Here we could send an email with the temporary password
        // For now, we will display it in the flash message

        return back()->with('status', "Request approved. User created with username: {$username} and temporary password: {$temporaryPassword}. They will be forced to change it upon login.");
    }

    /**
     * Deny the request.
     */
    public function deny(Request $request, UserAccessRequest $accessRequest)
    {
        if ($accessRequest->status !== 'pending') {
            return back()->with('error', 'Request has already been processed.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $accessRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('status', 'Request denied.');
    }
}
