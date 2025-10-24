<?php

namespace App\Http\Requests\Api;

class SearchRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'origin_location' => 'required|string|max:255',
            'destination_location' => 'required|string|max:255|different:origin_location',
            'date' => 'required|date_format:Y-m-d',
            'vehicle_type' => 'required|in:bus,train',

            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|gt:price_min',
            'time_of_day' => 'nullable|string|in:sang,chieu,toi',
            'per_page' => 'nullable|integer|min:1|max:50',
        ];
    }
}