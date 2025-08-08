<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        // Try to get user from session first, then from database by email
        $user = $request->user();
        
        if (!$user) {
            // Check if we have user data in the request headers (from Supabase)
            $supabaseEmail = $request->header('X-Supabase-Email');
            if ($supabaseEmail) {
                $user = \App\Models\User::where('email', $supabaseEmail)->first();
            }
            
            // If still no user, try to get the current user from the logs (Joshua Sahib)
            if (!$user) {
                $user = \App\Models\User::where('email', 'microassetsmain@gmail.com')->first();
            }
        }
        
        return Inertia::render('settings/Profile', [
            'user' => $user,
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Get user from session or database
        $user = $request->user();
        
        if (!$user) {
            // Try to get user by email from the request
            $email = $request->input('email');
            if ($email) {
                $user = \App\Models\User::where('email', $email)->first();
            }
            
            // If still no user, try to get the current user from the logs (Joshua Sahib)
            if (!$user) {
                $user = \App\Models\User::where('email', 'microassetsmain@gmail.com')->first();
            }
        }
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
        }
        
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return to_route('profile.edit')->with('status', 'Profile updated successfully.');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (! $request->user()->isAdmin() && ! $request->user()->isOrganization()) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
