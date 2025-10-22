<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule; 

class UpdateProfileRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        $userId = auth('api')->id(); 

        return [

            'name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|digits:10',


            'current_password' => 'required_with:password|string', 

            'password' => [
                'sometimes',
                'string',
                'confirmed', 
                Password::min(6),
            ],
        ];
    }
}