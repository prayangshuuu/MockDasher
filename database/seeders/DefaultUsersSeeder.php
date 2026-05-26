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

        // Create Admin – Prayangshu Biswas
        User::updateOrCreate(
            ['email' => 'admin@prayangshu.com'],
            [
                'name'       => 'Prayangshu Biswas',
                'first_name' => 'Prayangshu',
                'last_name'  => 'Biswas',
                'password'   => Hash::make('password'),
            ]
        )->roles()->sync([$adminRole->id]);

        // Create User – Daniel Rozario
        User::updateOrCreate(
            ['email' => 'user@prayangshu.com'],
            [
                'name'       => 'Daniel Rozario',
                'first_name' => 'Daniel',
                'last_name'  => 'Rozario',
                'password'   => Hash::make('password'),
            ]
        )->roles()->sync([$userRole->id]);
    }
}
