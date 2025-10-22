<?php

namespace App\Http\Requests\Api\Vendor;

use App\Http\Requests\Api\ApiRequest;

class CreateTicketRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required','integer','exists:vehicles,id'],
            'start_date' => ['required','date'],
            'start_time' => ['required','regex:/^\d{2}:\d{2}-\d{2}:\d{2}$/'],
            'from_city'  => ['required','string','min:1'],
            'to_city'    => ['required','string','min:1','different:from_city'],
            'price'      => ['required','numeric','min:0'],
        ];
    }
}
