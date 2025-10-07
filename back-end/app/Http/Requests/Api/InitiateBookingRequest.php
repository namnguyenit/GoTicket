<?php

namespace App\Http\Requests\Api;

// Quan trọng: Kế thừa từ ApiRequest để có chuẩn báo lỗi JSON
class InitiateBookingRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Middleware 'auth:api' đã lo việc kiểm tra đăng nhập
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'trip_id' => 'required|integer|exists:trips,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'required|integer|exists:seats,id',
        ];
    }
}