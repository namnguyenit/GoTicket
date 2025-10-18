<?php

namespace App\Http\Requests\Api\Vendor;

use App\Http\Requests\Api\ApiRequest;

class UpdateVehicleRequest extends ApiRequest
{
    public function authorize(): bool{
        $vehicle = $this->route('vehicle');
        return $vehicle && $vehicle->vendor_id === $this->user()->vendor_id;
    }
    public function rules(): array{
        $vendorId = auth()->user()->vendor->id;
        $vehicleId = $this->route('vehicle')->id;
        return [
            'license_plate' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vehicles')->ignore($vehicleId)->where('vendor_id', $vendorId)
            ],
            'type' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
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
