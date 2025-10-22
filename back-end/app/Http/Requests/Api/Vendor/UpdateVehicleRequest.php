<?php

namespace App\Http\Requests\Api\Vendor;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends ApiRequest
{
    public function authorize(): bool{
        $vehicle = $this->route('vehicle');

        return $vehicle && $this->user() && optional($this->user()->vendor)->id === $vehicle->vendor_id;
    }
    public function rules(): array{
        $vehicleId = $this->route('vehicle')->id;
        return [
            'name' => 'sometimes|string|max:100',
            'vehicle_type' => ['sometimes', Rule::in(['bus','train'])],
            'license_plate' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('vehicles','license_plate')->ignore($vehicleId)
            ],

        ];
    }
    public function messages(): array
    {
        return [
            'license_plate.required' => 'Biển số xe là bắt buộc.',
            'license_plate.unique' => 'Biển số xe này đã tồn tại trong hệ thống của bạn.',
            'type.required' => 'Loại xe là bắt buộc.',
            'capacity.required' => 'Số chỗ ngồi là bắt buộc.',
            'capacity.integer' => 'Số chỗ ngồi phải là một số nguyên.',
        ];
    }
}
