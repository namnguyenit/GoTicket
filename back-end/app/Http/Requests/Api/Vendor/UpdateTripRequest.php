<?php

namespace App\Http\Requests\Api\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        return [
            'vendor_route_id' => ['sometimes','integer','exists:vendor_routes,id'],
            'departure_datetime' => ['sometimes','date'],
            'arrival_datetime' => ['sometimes','date','after:departure_datetime'],
            'base_price' => ['sometimes','numeric','min:0'],
            'status' => ['sometimes','in:scheduled,cancelled'],

            'stops' => ['sometimes','array'],
            'stops.*.stop_id' => ['required','integer','exists:stops,id'],
            'stops.*.stop_type' => ['required','in:pickup,dropoff'],
            'stops.*.scheduled_time' => ['required','date'],
        ];
    }
}
