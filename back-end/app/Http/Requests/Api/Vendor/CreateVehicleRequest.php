<?php

namespace App\Http\Requests\Api\Vendor;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class CreateVehicleRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vendorId = auth()->user()->vendor->id;

        $rules = [
            'name' => 'required|string|max:100',
            'vehicle_type' => ['required', Rule::in(['bus', 'train'])],
            'license_plate' => 'nullable|string|max:50|unique:vehicles,license_plate',
        ];

        $vehicleType = $this->input('vehicle_type');

        if ($vehicleType === 'bus') {
            $rules = array_merge($rules, [
                'coach' => 'required|array',
                'coach.coach_type' => ['required', Rule::in(['sleeper_vip', 'sleeper_regular', 'limousine'])],
                'coach.total_seats' => 'required|integer|min:1',
            ]);
        }

        if ($vehicleType === 'train') {
            $rules = array_merge($rules, [
                'coaches' => 'required|array|min:1',
                'coaches.*.coach_type' => ['required', Rule::in(['seat_soft', 'seat_VIP'])],

                'coaches.*.quantity' => 'required|integer|min:1',
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {

        return [
            'name.required' => 'Tên phương tiện là bắt buộc.',
            'vehicle_type.required' => 'Loại phương tiện là bắt buộc.',
            'coach.required' => 'Thông tin chi tiết của xe bus là bắt buộc.',
            'coaches.required' => 'Thông tin các toa tàu là bắt buộc.',
            'coaches.*.coach_type.in' => 'Loại toa tàu không hợp lệ.',
        ];
    }
}
