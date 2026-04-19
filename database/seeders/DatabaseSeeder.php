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
            ['email' => 'test.app@example.com'],
            [
                'name' => 'Test App User',
                'password' => bcrypt('password'),
                'is_manager' => false,
                'is_verified' => false, // Marked as verified for testing
            ]
        );

        User::updateOrCreate(
            ['email' => 'test.eng@example.com'],
            [
                'name' => 'Test Engineering User',
                'password' => bcrypt('password'),
                'is_manager' => false,
                'is_verified' => false, // Marked as verified for testing
            ]
        );

        User::updateOrCreate(
            ['email' => 'test.ops@example.com'],
            [
                'name' => 'Test Operations User',
                'password' => bcrypt('password'),
                'is_manager' => false,
                'is_verified' => true, // Marked as verified for testing
            ]
        );

        User::updateOrCreate(
            ['email' => 'test.mech@example.com'],
            [
                'name' => 'Test Mechanical User',
                'password' => bcrypt('password'),
                'is_manager' => false,
                'is_verified' => true, // Marked as verified for testing
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager.app@example.com'],
            [
                'name' => 'Manager App User',
                'password' => bcrypt('password'),
                'is_manager' => true,
                'is_verified' => false, // Unverified - pending super admin approval
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager.eng@example.com'],
            [
                'name' => 'Manager Engineering User',
                'password' => bcrypt('password'),
                'is_manager' => true,
                'is_verified' => false, // Unverified - pending super admin approval
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager.ops@example.com'],
            [
                'name' => 'Manager Operations User',
                'password' => bcrypt('password'),
                'is_manager' => true,
                'is_verified' => false, // Unverified - pending super admin approval
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager.mech@example.com'],
            [
                'name' => 'Manager Mechanical User',
                'password' => bcrypt('password'),
                'is_manager' => true,
                'is_verified' => false, // Unverified - pending super admin approval
            ]
        );

        User::updateOrCreate(
            ['email' => 'store.keeper@example.com'],
            [
                'name' => 'Resource Officer Tester',
                'password' => bcrypt('password'),
                'is_resource_officer' => true,
                'is_verified' => true, // Resource officers should also be verified
            ]
        );

  
    }
}
