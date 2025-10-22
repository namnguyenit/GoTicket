<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule; // ✅ THÊM DÒNG NÀY

class UpdateProfileRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        $userId = auth('api')->id(); // Lấy ID của người dùng đang đăng nhập

        return [

            'name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|digits:10',


            'current_password' => 'required_with:password|string', 

            'password' => [
                'sometimes',
                'string',
                'confirmed', // Yêu cầu phải có trường 'password_confirmation'
                Password::min(6),
            ],
        ];
    }
}