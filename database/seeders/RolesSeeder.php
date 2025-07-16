<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin'],
            ['name' => 'Account Owner'],
            ['name' => 'Account Billing'],
            ['name' => 'Account Viewer'],
            ['name' => 'Account User'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
