<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@techstore.com'],
            [
                'name' => 'Admin TechStore',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create test user
        User::firstOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
    }
}
