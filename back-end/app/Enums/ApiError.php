<?php

namespace App\Enums;

enum ApiError: string
{
    // Lỗi chung
    case NOT_FOUND = 'NOT_FOUND';
    case SERVER_ERROR = 'SERVER_ERROR';
    case UNAUTHORIZED = 'UNAUTHORIZED';
    case FORBIDDEN = 'FORBIDDEN';

    // Lỗi liên quan đến xác thực & người dùng
    case VALIDATION_FAILED = 'VALIDATION_FAILED';
    case AUTHENTICATION_FAILED = 'AUTHENTICATION_FAILED';
    case EMAIL_ALREADY_EXISTS = 'EMAIL_ALREADY_EXISTS';

    // ... Thêm các loại lỗi khác của bạn ở đây

    public function getHttpCode(): int
    {
        return match ($this) {
            self::NOT_FOUND => 404,
            self::UNAUTHORIZED, self::AUTHENTICATION_FAILED => 401,
            self::FORBIDDEN => 403,
            self::VALIDATION_FAILED, self::EMAIL_ALREADY_EXISTS => 422,
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
            default => 'Lỗi không xác định.',
        };
    }
}