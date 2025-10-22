<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;

class CreateVendorRequest extends ApiRequest
{
    public function authorize(): bool
    {
        // Đảm bảo chỉ Admin mới có quyền tạo
        return auth('api')->user()->role === 'admin'; 
    }

    public function rules(): array
    {
        return [
            // Thông tin User (Người đại diện)
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string|digits:10',

            // Thông tin Vendor (Công ty)
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ];
    }
}