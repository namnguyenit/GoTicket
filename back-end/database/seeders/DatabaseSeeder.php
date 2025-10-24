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
            BulkVendorTripsSeeder::class, // 5. Bơm thêm >=100 chuyến mỗi nhà xe để test phân trang
            HNToNAOct30Seeder::class, // 6. Thêm đúng 100 chuyến ngày 30/10/2025 từ Hà Nội -> Nghệ An
        ]);
    }
}
