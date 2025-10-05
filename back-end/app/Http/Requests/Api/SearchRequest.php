<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends ApiRequest
{
    public function authorize(): bool
    {
        // Mọi người đều có quyền đăng ký
        return true;
    }

    public function rules(): array
    {
        return [
            'origin_location' => 'required|string|max:255',
            'destination_location' => 'required|string|max:255',
        ];
    }
}