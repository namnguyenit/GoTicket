<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <-- Import Hash facade

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Sử dụng firstOrCreate để chỉ tạo nếu chưa tồn tại
        // Nó sẽ tìm user có email là 'admin@vexe.com'
        User::firstOrCreate(
            [
                'email' => 'admin@vexe.com'
            ],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'), 
                'phone_number' => '0987654321',
                'role' => 'admin', 
            ]
        );

        $this->call([
            LocationsSeeder::class, 
            RoutesSeeder::class,
            TestDataSeeder::class    
        ]);
    }
}