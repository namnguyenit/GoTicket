<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor; 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::firstOrCreate(
            ['email' => 'admin@vexe.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'),
                'phone_number' => '0987654321',
                'role' => 'admin',
            ]
        );


        $phuongTrangUser = User::firstOrCreate(
            ['email' => 'nhaxephuongtrang@example.com'],
            [
                'name' => 'Nhà Xe Phương Trang',
                'password' => Hash::make('123456'),
                'phone_number' => '19006067',
                'role' => 'vendor'
            ]
        );
        Vendor::firstOrCreate(
            ['user_id' => $phuongTrangUser->id],
            ['company_name' => 'Công ty Phương Trang', 'status' => 'active']
        );

        $thanhBuoiUser = User::firstOrCreate(
            ['email' => 'nhaxethanhbuoi@example.com'],
            [
                'name' => 'Nhà Xe Thành Bưởi',
                'password' => Hash::make('123456'),
                'phone_number' => '19006079',
                'role' => 'vendor'
            ]
        );
        Vendor::firstOrCreate(
            ['user_id' => $thanhBuoiUser->id],
            ['company_name' => 'Công ty Thành Bưởi', 'status' => 'active']
        );
        
        // === TẠO TÀI KHOẢN KHÁCH HÀNG (CUSTOMER) ĐỂ TEST ===
        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Khách Hàng A',
                'password' => Hash::make('123456'),
                'phone_number' => '0123456789',
                'role' => 'customer'
            ]
        );

        // === CHẠY CÁC SEEDER KHÁC ===
        $this->call([
            LocationsSeeder::class,
            RoutesSeeder::class,
            StopsSeeder::class,
            TestDataSeeder::class
        ]);
    }
}