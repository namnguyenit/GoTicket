<?php

namespace App\Http\Requests\Api;

class ConfirmBookingRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => 'required|integer|exists:trips,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'required|integer|exists:seats,id',
            'pickup_stop_id' => 'required|integer|exists:stops,id',
            'dropoff_stop_id' => 'required|integer|exists:stops,id',
        ];
    }
}