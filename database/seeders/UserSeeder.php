<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        // Fetch all users from Supabase auth.users
        $supabaseApiKey = config('services.supabase.service_role_key');
        $supabaseUrl = config('services.supabase.url');
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
                        'min_endpointing_delay' => $sUser['user_metadata']['min_endpointing_delay'] ?? 0.5,
                        'max_endpointing_delay' => $sUser['user_metadata']['max_endpointing_delay'] ?? 6.0,
                        'min_speech_duration' => $sUser['user_metadata']['min_speech_duration'] ?? 0.05,
                        'min_silence_duration' => $sUser['user_metadata']['min_silence_duration'] ?? 0.55,
                        'prefix_padding_duration' => $sUser['user_metadata']['prefix_padding_duration'] ?? 0.5,
                        'max_buffered_speech' => $sUser['user_metadata']['max_buffered_speech'] ?? 60,
                        'activation_threshold' => $sUser['user_metadata']['activation_threshold'] ?? 0.5,
                    ]
                );
                $user->accounts()->syncWithoutDetaching([$account->id]);
                $user->roles()->syncWithoutDetaching([$role->id => ['account_id' => $account->id]]);
            }
        }

        // Remove the demo user creation loop at the end of run(). Only sync real Supabase users.
    }

    public function createConvos(User $user, Account $account) 
    {
        $convos = [
            'Example 1 - My day at the ball park',
            'Example 2 - My grandson visted',
            'Example 3 - My son call me today',
            'Example 4 - I am lonely',
            'Example 5 - I had a great day',
        ];

        foreach ($convos as $_convo) {
            $now = Carbon::now();
            $timeKeep = $now;

            $convo = Conversation::create([
                'name' => $_convo,
                'start_time' => $timeKeep,
                'end_time' => $timeKeep,
                'total_time_seconds' => 0,
                'account_id' => $account->id,
                'user_id' => $user->id,
            ]);

            $messageCount = range(1, rand(3, 15));

            foreach($messageCount as $count) {
                $startTime = $timeKeep;
                $endTime = 'time';

            }
        }
    }
}
