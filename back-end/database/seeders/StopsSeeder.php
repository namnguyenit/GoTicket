<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stops;
use Illuminate\Support\Facades\DB;

class StopsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Stops::truncate(); // Xóa dữ liệu cũ

        $stops = [
            // Điểm dừng ở Hà Nội
            ['name' => 'Bến xe Mỹ Đình', 'address' => '20 Phạm Hùng, Mỹ Đình, Nam Từ Liêm, Hà Nội'],
            ['name' => 'Bến xe Giáp Bát', 'address' => 'Giải Phóng, Giáp Bát, Hoàng Mai, Hà Nội'],
            ['name' => 'Bến xe Nước Ngầm', 'address' => 'Km số 8, Giải Phóng, Hoàng Mai, Hà Nội'],
            ['name' => 'Văn phòng Hà Nội', 'address' => 'Số 1 Trần Khát Chân, Hai Bà Trưng, Hà Nội'],

            // Điểm dừng ở TP. Hồ Chí Minh
            ['name' => 'Bến xe Miền Đông Mới', 'address' => '501 Hoàng Hữu Nam, Long Bình, Thủ Đức, TP.HCM'],
            ['name' => 'Bến xe Miền Tây', 'address' => '395 Kinh Dương Vương, An Lạc, Bình Tân, TP.HCM'],
            ['name' => 'Văn phòng Sài Gòn', 'address' => '239 Đề Thám, Phường Phạm Ngũ Lão, Quận 1, TP.HCM'],
        ];

        foreach ($stops as $stop) {
            Stops::create($stop);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}