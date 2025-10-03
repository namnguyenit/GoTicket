<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiError;

abstract class ApiRequest extends FormRequest
{
    use ResponseHelper; // Sử dụng Helper

    // Ghi đè phương thức xử lý khi validation thất bại
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        // Tạo response lỗi bằng Enum và Helper của chúng ta
        $response = $this->error(ApiError::VALIDATION_FAILED, $errors);

        throw new HttpResponseException($response);
    }
}