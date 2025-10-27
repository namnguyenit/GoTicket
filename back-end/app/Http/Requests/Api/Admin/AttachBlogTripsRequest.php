<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AttachBlogTripsRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Đã được bảo vệ bởi middleware role:admin ở routes
    }

    public function rules()
    {
        return [
            'trip_ids' => ['required','array','min:1'],
            'trip_ids.*' => ['integer','exists:trips,id'],
            'sync' => ['sometimes','boolean'],
        ];
    }
}
