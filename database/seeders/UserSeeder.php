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
            'Normal User' => Account::create(['name' => 'Normal User Account']),
            'Caregiver' => Account::create(['name' => 'Caregiver Account']),
            'Organization' => Account::create(['name' => 'Organization Account']),
            'Admin' => Account::create(['name' => 'Admin Account']),
        ];

        foreach ($roles as $roleName => $role) {
            $user = User::factory()->create([
                'name' => $roleName . ' User',
                'email' => strtolower(str_replace(' ', '', $roleName)) . '@example.com',
                'password' => $password,
                'current_account_id' => $accounts[$roleName]->id,
            ]);
            $user->accounts()->attach($accounts[$roleName]->id);
            $user->roles()->attach($role->id, ['account_id' => $accounts[$roleName]->id]);
            $this->createConvos($user, $accounts[$roleName]);
        }
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
