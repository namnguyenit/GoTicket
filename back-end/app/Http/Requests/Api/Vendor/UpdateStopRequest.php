<?php

namespace App\Http\Requests\Api\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Stops;
use Illuminate\Support\Facades\Auth;

class UpdateStopRequest extends FormRequest
{
    public function authorize(): bool
    {
        $stop = $this->route('stop');
        if ($stop instanceof Stops) {
            return Auth::check() && Auth::user()?->vendor && $stop->vendor_id === Auth::user()->vendor->id;
        }
        return false;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes','string','max:255'],
            'address' => ['sometimes','string','max:500'],
            'location_id' => ['sometimes','integer','exists:locations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Tên điểm dừng phải là chuỗi.',
            'address.string' => 'Địa chỉ phải là chuỗi.',
            'location_id.exists' => 'Tỉnh/thành phố được chọn không hợp lệ.',
        ];
    }
}
