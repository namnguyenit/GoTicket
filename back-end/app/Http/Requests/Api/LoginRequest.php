<?php

namespace App\Http\Requests\Api;

// Kế thừa từ ApiRequest để có thể trả về lỗi chuẩn
class LoginRequest extends ApiRequest
{
    public function authorize(): bool
    {
        // Mọi người đều có quyền thử đăng nhập
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }
}