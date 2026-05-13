<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles
        /** @var Role $adminRole */
        $adminRole = Role::query()->firstOrCreate(['name' => 'Admin']);
        /** @var Role $userRole */
        $userRole = Role::query()->firstOrCreate(['name' => 'User']);

        // Create Admin
        User::updateOrCreate(
            ['email' => 'admin@prayangshu.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        )->roles()->sync([$adminRole->id]);

        // Create User
        User::updateOrCreate(
            ['email' => 'user@prayangshu.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
            ]
        )->roles()->sync([$userRole->id]);
    }
}
