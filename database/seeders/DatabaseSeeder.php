<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Added back

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $userRole = Role::firstOrCreate(['name' => 'User']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@mockdasher.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        if (!$admin->roles()->where('name', 'Admin')->exists()) {
            $admin->roles()->attach($adminRole);
        }

        $user = User::firstOrCreate(
            ['email' => 'user@mockdasher.test'],
            [
                'name' => 'Test Access',
                'password' => Hash::make('password'),
            ]
        );

        if (!$user->roles()->where('name', 'User')->exists()) {
            $user->roles()->attach($userRole);
        }
    }
}
