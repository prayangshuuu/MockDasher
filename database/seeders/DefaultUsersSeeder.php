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
        /** @var User|null $admin */
        $admin = User::query()->where(fn ($q) => $q->where('email', 'prayangshu073@gmail.com'))->first();
        if (! $admin) {
            $admin = User::create([
                'email' => 'prayangshu073@gmail.com',
                'name' => 'Admin User',
                'password' => Hash::make('MockDasher@TST'),
            ]);
        }
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // Create User
        /** @var User|null $user */
        $user = User::query()->where(fn ($q) => $q->where('email', 'prayangshuuu@gmail.com'))->first();
        if (! $user) {
            $user = User::create([
                'email' => 'prayangshuuu@gmail.com',
                'name' => 'Regular User',
                'password' => Hash::make('MockDasher@TST'),
            ]);
        }
        $user->roles()->syncWithoutDetaching([$userRole->id]);
    }
}
