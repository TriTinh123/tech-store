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
        User::updateOrCreate(
            ['role' => 'admin'],
            [
                'name'               => 'Admin TechStore',
                'email'              => env('ADMIN_EMAIL', 'nguyentrungtritinh2005@gmail.com'),
                'password'           => Hash::make('admin123'),
                'role'               => 'admin',
                'email_verified_at'  => now(),
            ]
        );

        // Create test user
        User::firstOrCreate(
            ['email' => 'nguyentrungtritinh2005@gmail.com'],
            [
                'name'               => 'Test User',
                'password'           => Hash::make('password123'),
                'role'               => 'user',
                'email_verified_at'  => now(),
            ]
        );
    }
}
