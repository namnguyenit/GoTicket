<?php

namespace App\Http\Requests\Api;

class UpdateUserRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|digits:10',
            'role' => 'sometimes|string',
        ];
    }
}
