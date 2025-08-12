<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ElderlyProfile;
use App\Services\ElderlyProfileRealtimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Show admin login form
     */
    public function showLogin()
    {
        return Inertia::render('admin/Login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user has Admin or Administration role
            if ($user->roles()->whereIn('name', ['Admin', 'Administration'])->exists()) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. Admin privileges required.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'totalUsers' => User::count(),
            'adminUsers' => User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Admin', 'Administration']);
            })->count(),
            'elderlyProfiles' => ElderlyProfile::count(),
            'conversations' => 0, // TODO: Add conversation count when model is available
        ];

        return Inertia::render('admin/AdminDashboard', [
            'stats' => $stats,
        ]);
    }

    /**
     * Show user management page
     */
    public function users(Request $request)
    {
        $query = User::with('roles');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all();

        return Inertia::render('admin/Users', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Remove all existing roles and add new one
        $user->roles()->detach();
        $user->roles()->attach($request->role_id, ['account_id' => 1]);

        return back()->with('success', 'User role updated successfully.');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // Prevent deleting the current admin user
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Cannot delete your own account.']);
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    /**
     * Show elderly profiles management
     */
    public function elderlyProfiles(Request $request)
    {
        $query = ElderlyProfile::with('user');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('associated_account_email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $profiles = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('admin/ElderlyProfiles', [
            'profiles' => $profiles,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Update elderly profile status
     */
    public function updateProfileStatus(Request $request, ElderlyProfile $profile)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,deactivated',
        ]);

        $profile->update(['status' => $request->status]);

        // Sync to Supabase
        $realtimeService = new ElderlyProfileRealtimeService();
        $realtimeService->pushToSupabase($profile, 'update');

        return back()->with('success', 'Profile status updated successfully.');
    }

    /**
     * Update elderly profile
     */
    public function updateProfile(Request $request, ElderlyProfile $profile)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,deactivated',
            'associated_account_email' => 'nullable|email|max:255',
        ]);

        $profile->update($request->only([
            'name', 'email', 'phone', 'status', 'associated_account_email'
        ]));

        // Sync to Supabase
        $realtimeService = new ElderlyProfileRealtimeService();
        $realtimeService->pushToSupabase($profile, 'update');

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete elderly profile
     */
    public function deleteProfile(ElderlyProfile $profile)
    {
        // Sync to Supabase before deleting
        $realtimeService = new ElderlyProfileRealtimeService();
        $realtimeService->pushToSupabase($profile, 'delete');

        $profile->delete();
        return back()->with('success', 'Profile deleted successfully.');
    }

    /**
     * Sync elderly profiles from Supabase
     */
    public function syncFromSupabase()
    {
        try {
            $realtimeService = new ElderlyProfileRealtimeService();
            $success = $realtimeService->syncAllProfiles();
            
            if ($success) {
                return response()->json(['message' => 'Profiles synced successfully']);
            } else {
                return response()->json(['error' => 'Failed to sync profiles'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error during sync: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Admin logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
} 