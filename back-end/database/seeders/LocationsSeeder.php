<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationsSeeder extends Seeder
{
    public function run(): void
    {
        Location::insert([
            ['name' => 'Hồ Chí Minh'],
            ['name' => 'Hà Nội'],
            ['name' => 'Đà Nẵng'],
            ['name' => 'Lâm Đồng'],
            ['name' => 'Khánh Hòa'],
        ]);
    }
}
