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
            'origin_location' => 'nullable|string|max:255',
            'destination_location' => 'nullable|string|max:255|different:origin_location',
            'date' => 'nullable|date_format:Y-m-d',
            'vehicle_type' => 'nullable|in:bus,train',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|gt:price_min',
            'time_of_day' => 'nullable|string|in:sang,chieu,toi',
            'coach_types' => 'nullable|array',
            'coach_types.*' => 'string',
            'per_page' => 'nullable|integer|min:1|max:50',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $data = $this->all();
            $hasDate = !empty($data['date']);
            $hasOrigin = !empty($data['origin_location']);
            $hasDestination = !empty($data['destination_location']);

            if (!$hasDate && !($hasOrigin && $hasDestination)) {
                $v->errors()->add('query', 'Cần nhập ngày hoặc cả điểm xuất phát và điểm đến.');
            }
            if (($hasOrigin xor $hasDestination)) {
                $v->errors()->add('origin_destination', 'Khi tìm theo địa điểm, cần cả điểm xuất phát và điểm đến.');
            }
        });
    }
}
