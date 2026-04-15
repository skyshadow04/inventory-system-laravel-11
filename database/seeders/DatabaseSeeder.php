<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'is_superadmin' => true,
                'is_verified' => true, // Admin is always verified
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'is_manager' => false,
                'is_verified' => true, // Marked as verified for testing
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => bcrypt('password'),
                'is_manager' => true,
                'is_verified' => false, // Unverified - pending super admin approval
            ]
        );

        User::updateOrCreate(
            ['email' => 'resource_officer@example.com'],
            [
                'name' => 'Resource Officer Tester',
                'password' => bcrypt('password'),
                'is_resource_officer' => true,
                'is_verified' => true, // Resource officers should also be verified
            ]
        );

  
    }
}
