<?php

namespace App\Enums;

use Symfony\Component\Mailer\Messenger\SendEmailMessage;

enum ApiError: string
{

    case NOT_FOUND = 'NOT_FOUND';
    case SERVER_ERROR = 'SERVER_ERROR';
    case UNAUTHORIZED = 'UNAUTHORIZED';
    case FORBIDDEN = 'FORBIDDEN';

    case VALIDATION_FAILED = 'VALIDATION_FAILED';
    case AUTHENTICATION_FAILED = 'AUTHENTICATION_FAILED';
    case EMAIL_ALREADY_EXISTS = 'EMAIL_ALREADY_EXISTS';
    case EMAIL_NOT_EXISTS = 'EMAIL_NOT_EXISTS';
    case WRONG_PASSWORD = "WRONG_PASSWORD";
    case DATA_NULL = "DATA_NULL";




    case VENDOR_NOT_ASSOCIATED = "VENDOR_NOT_ASSOCIATED";
    case VENDOR_INACTIVE = "VENDOR_INACTIVE";
    case VEHICLE_NOT_FOUND = "VEHICLE_NOT_FOUND";
    case VEHICLE_LICENSE_EXISTS = "VEHICLE_LICENSE_EXISTS";
    case VENDOR_ROUTE_NOT_FOUND = "VENDOR_ROUTE_NOT_FOUND";
    case VENDOR_ROUTE_STOP_INVALID = "VENDOR_ROUTE_STOP_INVALID";
    case VENDOR_ROUTE_STOP_DUPLICATE = "VENDOR_ROUTE_STOP_DUPLICATE";
    case TRIP_NOT_FOUND = "TRIP_NOT_FOUND";
    case TRIP_INVALID_STATUS = "TRIP_INVALID_STATUS";
    case TRIP_TIME_CONFLICT = "TRIP_TIME_CONFLICT";
    case TRIP_COACH_NOT_FOUND = "TRIP_COACH_NOT_FOUND";
    case SEAT_NOT_FOUND = "SEAT_NOT_FOUND";
    case SEAT_STATE_CONFLICT = "SEAT_STATE_CONFLICT";
    case SEAT_PRICE_INVALID = "SEAT_PRICE_INVALID";
    case BOOKING_NOT_FOUND = "BOOKING_NOT_FOUND";
    case BOOKING_CANCEL_NOT_ALLOWED = "BOOKING_CANCEL_NOT_ALLOWED";
    case PAYMENT_NOT_FOUND = "PAYMENT_NOT_FOUND";
    case PAYMENT_STATUS_CONFLICT = "PAYMENT_STATUS_CONFLICT";
    case ROUTE_NOT_FOUND = "ROUTE_NOT_FOUND";





    case ACCOUNT_INACTIVE = "ACCOUNT_INACTIVE";


    public function getHttpCode(): int
    {
        return match ($this) {
            self::NOT_FOUND => 404,
            self::UNAUTHORIZED, self::AUTHENTICATION_FAILED => 401,
            self::FORBIDDEN => 403,
            self::VALIDATION_FAILED, self::EMAIL_ALREADY_EXISTS => 422,
            self::EMAIL_NOT_EXISTS ,self::WRONG_PASSWORD => 423,
            self::DATA_NULL => 201,
<<<<<<< HEAD
            self::FORBIDDEN, self::ACCOUNT_INACTIVE => 403,
=======

            self::VENDOR_NOT_ASSOCIATED => 403,
            self::VENDOR_INACTIVE => 403,
            self::VEHICLE_NOT_FOUND => 404,
            self::VEHICLE_LICENSE_EXISTS => 409,
            self::VENDOR_ROUTE_NOT_FOUND => 404,
            self::VENDOR_ROUTE_STOP_INVALID => 422,
            self::VENDOR_ROUTE_STOP_DUPLICATE => 409,
            self::TRIP_NOT_FOUND => 404,
            self::TRIP_INVALID_STATUS => 422,
            self::TRIP_TIME_CONFLICT => 409,
            self::TRIP_COACH_NOT_FOUND => 404,
            self::SEAT_NOT_FOUND => 404,
            self::SEAT_STATE_CONFLICT => 409,
            self::SEAT_PRICE_INVALID => 422,
            self::BOOKING_NOT_FOUND => 404,
            self::BOOKING_CANCEL_NOT_ALLOWED => 422,
            self::PAYMENT_NOT_FOUND => 404,
            self::PAYMENT_STATUS_CONFLICT => 422,

>>>>>>> origin/main
            default => 500, // Mặc định là lỗi server
        };
    }

    public function getMessage(): string
    {
        return match ($this) {
            self::NOT_FOUND => 'Không tìm thấy tài nguyên được yêu cầu.',
            self::SERVER_ERROR => 'Đã có lỗi xảy ra ở phía máy chủ.',
            self::UNAUTHORIZED => 'Chưa xác thực.',
            self::FORBIDDEN => 'Bạn không có quyền truy cập tài nguyên này.',
            self::VALIDATION_FAILED => 'Dữ liệu đầu vào không hợp lệ.',
            self::AUTHENTICATION_FAILED => 'Email hoặc mật khẩu không chính xác.',
            self::EMAIL_ALREADY_EXISTS => 'Email này đã tồn tại trong hệ thống.',
            self::WRONG_PASSWORD => 'Sai mật khẩu',
            self::EMAIL_NOT_EXISTS => 'email không tồn tại',
            self::DATA_NULL => 'data trống',
<<<<<<< HEAD
            self::ACCOUNT_INACTIVE => 'Tài khoản chưa được kích hoạt hoặc đã bị khóa.',
=======


            self::VENDOR_NOT_ASSOCIATED => "Tài khoản nhà xe chưa được cấp phép",
            self::VENDOR_INACTIVE => "Tài khoản nhà xe chưa được kích hoạt",
            self::VEHICLE_NOT_FOUND => "Không tìm thấy phương tiện",
            self::VEHICLE_LICENSE_EXISTS => "Biển số xe đã tồn tại",
            self::VENDOR_ROUTE_NOT_FOUND =>"Không tìm thấy tuyến nhà xe",
            self::VENDOR_ROUTE_STOP_INVALID =>"Điểm dừng không hợp lệ",
            self::VENDOR_ROUTE_STOP_DUPLICATE => "Điểm dừng đã tồn tại",
            self::TRIP_NOT_FOUND => "Không tìm thấy chuyến đi",
            self::TRIP_INVALID_STATUS =>"Trạng thái chuyến không cho phép thao tác này",
            self::TRIP_TIME_CONFLICT => "Thời gian chuyến xung đột",
            self::TRIP_COACH_NOT_FOUND => "Không tìm thấy chuyến xe",
            self::SEAT_NOT_FOUND => "Không tìm thấy ghế",
            self::SEAT_STATE_CONFLICT => "Trạng thái ghế xung đột",
            self::SEAT_PRICE_INVALID => "Giá ghế không hợp lệ",
            self::BOOKING_NOT_FOUND => "Không tìm thấy đơn đặt chỗ",
            self::BOOKING_CANCEL_NOT_ALLOWED =>"Đơn đặt chỗ không thể hủy",
            self::PAYMENT_NOT_FOUND => "Không tìm thấy thanh toán",
            self::PAYMENT_STATUS_CONFLICT => "Trạng thái thanh toán xung đột",

>>>>>>> origin/main
            default => 'Lỗi không xác định.',
        };
    }
}
