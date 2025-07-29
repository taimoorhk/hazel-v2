<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Role;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class SyncSupabaseUsers extends Command
{
    protected $signature = 'sync:supabase-users';
    protected $description = 'Sync all users and roles from Supabase auth.users to backend tables';

    public function handle()
    {
        $supabaseApiKey = config('services.supabase.service_role_key');
        $supabaseUrl = config('services.supabase.url');
        $password = Hash::make('password');
        $roles = [
            'Normal User' => Role::where('name', 'Normal User')->first(),
            'Caregiver' => Role::where('name', 'Caregiver')->first(),
            'Organization' => Role::where('name', 'Organization')->first(),
            'Admin' => Role::where('name', 'Admin')->first(),
        ];
        $accounts = [
            'Normal User' => Account::firstOrCreate(['name' => 'Normal User Account']),
            'Caregiver' => Account::firstOrCreate(['name' => 'Caregiver Account']),
            'Organization' => Account::firstOrCreate(['name' => 'Organization Account']),
            'Admin' => Account::firstOrCreate(['name' => 'Admin Account']),
        ];
        if ($supabaseApiKey && $supabaseUrl) {
            $response = Http::withHeaders([
                'apikey' => $supabaseApiKey,
                'Authorization' => 'Bearer ' . $supabaseApiKey,
            ])->get("$supabaseUrl/auth/v1/users");
            $supabaseUsers = $response->json('users') ?? [];
            foreach ($supabaseUsers as $sUser) {
                $roleName = $sUser['user_metadata']['role'] ?? 'Normal User';
                $role = $roles[$roleName] ?? $roles['Normal User'];
                $account = $accounts[$roleName] ?? $accounts['Normal User'];
                $user = User::firstOrCreate(
                    ['email' => $sUser['email']],
                    [
                        'name' => $sUser['user_metadata']['name'] ?? $sUser['email'],
                        'password' => $password,
                        'supabase_id' => $sUser['id'],
                        'current_account_id' => $account->id,
                        'user_questions' => $sUser['user_metadata']['user_questions'] ?? null,
                    ]
                );
                $user->accounts()->syncWithoutDetaching([$account->id]);
                $user->roles()->syncWithoutDetaching([$role->id => ['account_id' => $account->id]]);
            }
        }
        // After syncing from Supabase, also push any backend changes to Supabase auth.users
        foreach (User::with('roles')->get() as $user) {
            $latestRole = $user->roles()->latest('user_roles.created_at')->first();
            $latestRoleName = $latestRole ? $latestRole->name : 'Normal User';
            if ($supabaseApiKey && $supabaseUrl && $user->supabase_id) {
                Http::withHeaders([
                    'apikey' => $supabaseApiKey,
                    'Authorization' => 'Bearer ' . $supabaseApiKey,
                    'Content-Type' => 'application/json',
                ])->patch("$supabaseUrl/auth/v1/admin/users/{$user->supabase_id}", [
                    'user_metadata' => [
                        'role' => $latestRoleName,
                        'user_questions' => $user->user_questions,
                    ],
                ]);
            }
        }
        $this->info('Supabase users and roles synced to backend.');
    }
} 