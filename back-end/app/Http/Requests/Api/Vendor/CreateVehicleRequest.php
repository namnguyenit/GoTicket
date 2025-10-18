<?php

namespace App\Http\Requests\Api\Vendor;

use App\Http\Requests\Api\ApiRequest; // *** THAY ĐỔI QUAN TRỌNG ***

class CreateVehicleRequest extends ApiRequest // *** THAY ĐỔI QUAN TRỌNG ***
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vendorId = auth()->user()->vendor->id;

        return [
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate,NULL,id,vendor_id,' . $vendorId,
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
