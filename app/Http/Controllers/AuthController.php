<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function syncSupabaseUser(Request $request)
    {
        $supabaseId = $request->input('id');
        $email = $request->input('email');
        $name = $request->input('name', '');
        $role = $request->input('role', 'Normal User'); // default

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt(Str::random(32)),
                'supabase_id' => $supabaseId,
            ]
        );

        // If user already exists but supabase_id is not set, update it
        if (!$user->supabase_id) {
            $user->supabase_id = $supabaseId;
            $user->save();
        }

        // Attach role if not already attached
        $roleModel = Role::where('name', $role)->first();
        if ($roleModel && !$user->roles->contains($roleModel->id)) {
            $user->roles()->attach($roleModel->id, ['account_id' => 1]); // Adjust account_id as needed
        }

        return response()->json(['success' => true]);
    }
} 