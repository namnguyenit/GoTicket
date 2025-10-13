<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location; 
use App\Models\Routes;   
use Illuminate\Support\Facades\DB;

class RoutesSeeder extends Seeder
{

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Routes::truncate(); 


        $locations = Location::all();


        foreach ($locations as $origin) {
            foreach ($locations as $destination) {

                if ($origin->id !== $destination->id) {
                    Routes::create([
                        'origin_location_id' => $origin->id,
                        'destination_location_id' => $destination->id,
                    ]);
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}