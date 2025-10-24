<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\Api\ApiRequest;

class CreateVendorRequest extends ApiRequest
{
    public function authorize(): bool
    {

        return auth('api')->user()->role === 'admin'; 
    }

    public function rules(): array
    {
        return [

            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string|digits:10',


            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'logo_url' => 'nullable|url|max:2048',
            'logo_url' => 'nullable|url|max:2048',
        ];
    }
}