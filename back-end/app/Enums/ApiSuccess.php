<?php

namespace App\Enums;

enum ApiSuccess: string
{
    case GET_DATA_SUCCESS = 'GET_DATA_SUCCESS';
    case ACTION_SUCCESS = 'ACTION_SUCCESS';
    case CREATED_SUCCESS = 'CREATED_SUCCESS';

    public function getHttpCode(): int
    {
        return match ($this) {
            self::CREATED_SUCCESS => 201,
            default => 200,
        };
    }

    public function getMessage(): string
    {
        return match ($this) {
            self::GET_DATA_SUCCESS => 'Lấy dữ liệu thành công.',
            self::ACTION_SUCCESS => 'Thực hiện hành động thành công.',
            self::CREATED_SUCCESS => 'Đăng ký tài khoản thành công.', // Hoặc 'Tạo mới thành công'
        };
    }
}