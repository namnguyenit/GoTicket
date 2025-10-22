<?php

namespace App\Enums;

enum ApiSuccess: string
{
    case GET_DATA_SUCCESS = 'GET_DATA_SUCCESS';
    case ACTION_SUCCESS = 'ACTION_SUCCESS';
    case CREATED_SUCCESS = 'CREATED_SUCCESS';



    // Mã thành công cho nhà xe
    case VEHICLE_CREATED ='VEHICLE_CREATED';
    case VEHICLE_UPDATED ='VEHICLE_UPDATED';
    case VEHICLE_DELETED ='VEHICLE_DELETED';
    case VENDOR_ROUTE_CREATED ='VENDOR_ROUTE_CREATED';
    case VENDOR_ROUTE_UPDATED ='VENDOR_ROUTE_UPDATED';
    case VENDOR_ROUTE_DELETED ='VENDOR_ROUTE_DELETED';
    case TRIP_CREATED ='TRIP_CREATED';
    case TRIP_UPDATED ='TRIP_UPDATED';
    case TRIP_CANCELLED ='TRIP_CANCELLED';
    case SEAT_PRICES_UPDATED ='SEAT_PRICES_UPDATED';
    case SEATS_LOCKED ='SEATS_LOCKED';
    case SEATS_UNLOCKED ='SEATS_UNLOCKED';
    case BOOKING_CANCELLED = 'BOOKING_CANCELLED';

    public function getHttpCode(): int
    {
        return match ($this) {
            self::CREATED_SUCCESS => 201,
            self::VEHICLE_CREATED => 201,
            self::VEHICLE_UPDATED => 200,
            self::VEHICLE_DELETED => 200,
            self::VENDOR_ROUTE_CREATED => 201,
            self::VENDOR_ROUTE_UPDATED => 200,
            self::VENDOR_ROUTE_DELETED => 200,
            self::TRIP_CREATED => 201,
            self::TRIP_UPDATED => 200,
            self::TRIP_CANCELLED => 200,
            self::SEAT_PRICES_UPDATED => 200,
            self::SEATS_LOCKED => 200,
            self::SEATS_UNLOCKED => 200,
            self::BOOKING_CANCELLED => 200,

            default => 200,
        };
    }

    public function getMessage(): string
    {
        return match ($this) {
            self::GET_DATA_SUCCESS => 'Lấy dữ liệu thành công.',
            self::ACTION_SUCCESS => 'Thực hiện hành động thành công.',
            self::CREATED_SUCCESS => 'Đăng ký tài khoản thành công.',
            self::VEHICLE_CREATED => 'Tạo phương tiện thành công.',
            self::VEHICLE_UPDATED => 'Cập nhật phương tiện thành công.',
            self::VEHICLE_DELETED => 'Xoá phương tiện thành công.',
            self::VENDOR_ROUTE_CREATED => 'Tạo tuyến nhà xe thành công.',
            self::VENDOR_ROUTE_UPDATED => 'Cập nhật tuyến nhà xe thành công.',
            self::VENDOR_ROUTE_DELETED => 'Xoá tuyến nhà xe thành công.',
            self::TRIP_CREATED => 'Tạo chuyến đi thành công.',
            self::TRIP_UPDATED => 'Cập nhật chuyến đi thành công.',
            self::TRIP_CANCELLED => 'Huỷ chuyến đi thành công.',
            self::SEAT_PRICES_UPDATED => 'Cập nhật giá ghế thành công.',
            self::SEATS_LOCKED => 'Khoá ghế thành công.',
            self::SEATS_UNLOCKED => 'Mở khoá ghế thành công.',
            self::BOOKING_CANCELLED => 'Huỷ vé thành công.',
        };
    }
}
