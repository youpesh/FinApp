<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PasswordService $passwordService)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', Rule::in(['admin', 'manager', 'accountant'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended', 'pending'])],
        ]);

        $username = $passwordService->generateUsername($validated['first_name'], $validated['last_name']);

        // Generate a secure temporary password
        $temporaryPassword = \Illuminate\Support\Str::password(16);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $username,
            'email' => $validated['email'],
            'password' => $temporaryPassword,
            'role' => $validated['role'],
            'status' => $validated['status'],
            'password_expires_at' => now()->subDay(), // Force password change on first login
            'created_by' => auth()->id(),
        ]);

        $passwordService->saveToHistory($user, $user->password);

        // Send credentials email to the new user
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\AccessRequestApproved($user, $temporaryPassword));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send new user credentials email: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')->with('status', 'User created successfully. Username: ' . $username . '. Login credentials have been emailed to ' . $user->email . '.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'manager', 'accountant'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended', 'pending'])],
            'suspension_start_date' => ['nullable', 'date', 'required_if:status,suspended'],
            'suspension_end_date' => ['nullable', 'date', 'after:suspension_start_date'],
        ]);

        // Clear suspension dates if not suspended
        if ($validated['status'] !== 'suspended') {
            $validated['suspension_start_date'] = null;
            $validated['suspension_end_date'] = null;
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted successfully.');
    }
}
