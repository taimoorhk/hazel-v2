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
        $adminRole = Role::where('name', 'Admin')->first();

        $account = Account::create([
            'name' => 'Admin Account',
        ]);

        $user = User::factory()->create([
            'name'       => 'Admin User',
            'email'      => 'admin@example.com',
            'password'   => $password,
            'current_account_id' => $account->id,
        ]);

        $user->accounts()->attach($account->id);
        $user->roles()->attach($adminRole->id, ['account_id' => $account->id]);
        $this->createConvos($user, $account);
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
            ]);
            $convo->account()->attach($account->id);
            $convo->user()->attach($user->id);

            $messageCount = rang(1, rand(3, 15));

            foreach($messageCount as $count) {
                $startTime = $timeKeep;
                $endTime = 'time';

            }
        }
    }
}
