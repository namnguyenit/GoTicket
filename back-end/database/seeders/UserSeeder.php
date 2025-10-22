<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // --- TÀI KHOẢN ADMIN ---
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@goticket.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // --- CÁC TÀI KHOẢN NHÀ XE (VENDOR) ---
        User::create([
            'name' => 'Nhà xe Phương Trang',
            'email' => 'vendor1@goticket.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
        ]);
        User::create([
            'name' => 'Nhà xe Thành Bưởi',
            'email' => 'vendor2@goticket.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
        ]);

        // --- CÁC TÀI KHOẢN NGƯỜI DÙNG (USER) ---
        User::create([
            'name' => 'John Doe',
            'email' => 'user1@goticket.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
        User::create([
            'name' => 'Jane Smith',
            'email' => 'user2@goticket.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
    }
}
