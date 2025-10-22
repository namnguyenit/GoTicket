<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,      // 1. Tạo Users trước
            LocationsSeeder::class, // 2. Tạo Locations
            RoutesSeeder::class,    // 3. Tạo Routes
            TestDataSeeder::class,  // 4. Tạo dữ liệu phức tạp (quan trọng nhất)
        ]);
    }
}
