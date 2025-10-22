<?php

namespace App\Http\Requests\Api\Vendor;

use App\Http\Requests\Api\ApiRequest;

class AddCoachesRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coaches' => 'required|array|min:1',
            'coaches.*.coach_type' => 'required|in:seat_soft,seat_VIP',
            'coaches.*.quantity' => 'required|integer|min:1',
        ];
    }
}
