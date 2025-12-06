<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('company')->orderBy('created_at', 'desc');

        // Filter by role
        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by KYC status
        if ($request->has('kyc') && $request->kyc !== 'all') {
            $query->whereHas('company', function ($q) use ($request) {
                $q->where('kyc_status', $request->kyc);
            });
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($q) use ($search) {
                      $q->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->paginate(20);

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function show(User $user)
    {
        $user->load(['company.subnets', 'company.leasesAsHolder', 'company.leasesAsLessee', 'company.invoices', 'cartItems']);

        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Password::defaults()],
            'role' => 'required|in:user,admin',
            'status' => 'required|in:active,inactive,suspended',
            'verify_email' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'email_verified_at' => $request->has('verify_email') ? now() : null,
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', __('User created successfully.'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user->update($validated);

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', Password::defaults()],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Verify email if requested
        if ($request->has('verify_email') && !$user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', __('User updated successfully.'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('You cannot delete your own account.'));
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', __('Cannot delete the last admin user.'));
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', __('User deleted successfully.'));
    }

    public function suspend(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('You cannot suspend your own account.'));
        }

        $user->update(['status' => 'suspended']);

        return back()->with('success', __('User suspended successfully.'));
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);

        return back()->with('success', __('User activated successfully.'));
    }

    public function verifyEmail(User $user)
    {
        if ($user->email_verified_at) {
            return back()->with('info', __('Email is already verified.'));
        }

        $user->update(['email_verified_at' => now()]);

        return back()->with('success', __('Email verified successfully.'));
    }

    public function impersonate(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('You cannot impersonate yourself.'));
        }

        session(['impersonator_id' => auth()->id()]);
        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('warning', __('You are now impersonating :name. Click "Stop Impersonating" to return.', ['name' => $user->name]));
    }

    public function stopImpersonating()
    {
        $impersonatorId = session('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('dashboard');
        }

        $admin = User::find($impersonatorId);
        session()->forget('impersonator_id');

        if ($admin) {
            auth()->login($admin);
            return redirect()->route('admin.users.index')
                ->with('success', __('Stopped impersonating.'));
        }

        return redirect()->route('dashboard');
    }
}
