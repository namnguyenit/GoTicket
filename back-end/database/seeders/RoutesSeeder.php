<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Routes;

class RoutesSeeder extends Seeder
{
    public function run(): void
    {
        // Tuyến Sài Gòn - Đà Lạt
        Routes::create(['origin_location_id' => 1, 'destination_location_id' => 4]);
        // Tuyến Sài Gòn - Nha Trang
        Routes::create(['origin_location_id' => 1, 'destination_location_id' => 5]);
        // Tuyến Hà Nội - Đà Nẵng
        Routes::create(['origin_location_id' => 2, 'destination_location_id' => 3]);
    }
}
