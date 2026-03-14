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
        /** @var \App\Models\Role $adminRole */
        $adminRole = \App\Models\Role::query()->firstOrCreate(['name' => 'Admin']);
        /** @var \App\Models\Role $userRole */
        $userRole = \App\Models\Role::query()->firstOrCreate(['name' => 'User']);

        // Create Admin
        /** @var \App\Models\User $admin */
        $admin = \App\Models\User::query()->firstOrCreate(
            ['email' => 'prayangshu073@gmail.com']
        );
        if ($admin->wasRecentlyCreated) {
            $admin->update([
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('MockDasher@TST'),
            ]);
        }
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // Create User
        /** @var \App\Models\User $user */
        $user = \App\Models\User::query()->firstOrCreate(
            ['email' => 'prayangshuuu@gmail.com']
        );
        if ($user->wasRecentlyCreated) {
            $user->update([
                'name' => 'Regular User',
                'password' => \Illuminate\Support\Facades\Hash::make('MockDasher@TST'),
            ]);
        }
        $user->roles()->syncWithoutDetaching([$userRole->id]);
    }
}
