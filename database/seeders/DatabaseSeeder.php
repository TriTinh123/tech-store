<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo 10 user ngẫu nhiên
        User::factory(10)->create();

        // Tạo các user test cụ thể
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'nguyenvana@test.com',
            'password' => Hash::make('123456'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Trần Thị B',
            'email' => 'tranthib@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('test123456'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Seed categories and products
        $this->call(CategorySeeder::class);
        $this->call(PeripheralsSeeder::class);

        // Seed security questions for 3FA
        $this->call(SecurityQuestionSeeder::class);
    }
}
