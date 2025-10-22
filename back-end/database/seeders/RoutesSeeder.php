<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Routes;
use App\Models\Location;

class RoutesSeeder extends Seeder
{
    public function run(): void
    {
        $locations = Location::pluck('id','name');
        $pairs = [
            ['Hồ Chí Minh','Đà Nẵng'],['Hồ Chí Minh','Hà Nội'],['Hồ Chí Minh','Lâm Đồng'],['Hồ Chí Minh','Khánh Hòa'],
            ['Hà Nội','Đà Nẵng'],['Hà Nội','Quảng Ninh'],['Hà Nội','Ninh Bình'],['Hà Nội','Nghệ An'],
            ['Đà Nẵng','Quảng Nam'],['Đà Nẵng','Quảng Ngãi'],['Đà Nẵng','Thừa Thiên Huế'],
            ['Cần Thơ','Hồ Chí Minh'],['Cần Thơ','An Giang'],['Cần Thơ','Kiên Giang'],
        ];

        $names = array_keys($locations->toArray());
        for($i=0;$i<20;$i++){
            $a = $names[array_rand($names)];
            $b = $names[array_rand($names)];
            if($a === $b) continue;
            $pairs[] = [$a,$b];
        }
        foreach ($pairs as [$from,$to]){
            $o = $locations[$from] ?? null; $d = $locations[$to] ?? null;
            if($o && $d){
                Routes::firstOrCreate(['origin_location_id'=>$o,'destination_location_id'=>$d]);
            }
        }
    }
}
