<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles
        $adminRole = \App\Models\Role::firstOrCreate(['name' => 'Admin']);
        $userRole = \App\Models\Role::firstOrCreate(['name' => 'User']);

        // Create Admin
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'prayangshu073@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('MockDasher@TST'),
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // Create User
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'prayangshuuu@gmail.com'],
            [
                'name' => 'Regular User',
                'password' => \Illuminate\Support\Facades\Hash::make('MockDasher@TST'),
            ]
        );
        $user->roles()->syncWithoutDetaching([$userRole->id]);
    }
}
