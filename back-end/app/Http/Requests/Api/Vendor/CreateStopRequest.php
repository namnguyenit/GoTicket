<?php

namespace App\Http\Requests\Api\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class CreateStopRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'location_id' => 'required|integer|exists:locations,id',
        ];
    }
    public function messages(): array{
        return [
            'name.required' => 'Tên điểm dừng là bắt buộc.',
            'address.required' => 'Địa chỉ là bắt buộc.',
            'location_id.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'location_id.exists' => 'Tỉnh/thành phố được chọn không hợp lệ.',
        ];
    }
}
