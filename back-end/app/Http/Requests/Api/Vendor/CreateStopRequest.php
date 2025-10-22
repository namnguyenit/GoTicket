<?php

namespace App\Http\Requests\Api\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class CreateStopRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Yêu cầu đăng nhập và có vendor
        return auth()->check() && auth()->user()?->vendor()->exists();
    }

    protected function prepareForValidation(): void
    {
        $vendorId = auth()->user()?->vendor?->id;
        if ($vendorId) {
            $this->merge(['vendor_id' => $vendorId]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            // Không cho client gửi, nhưng validate giá trị đã merge từ server
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
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
