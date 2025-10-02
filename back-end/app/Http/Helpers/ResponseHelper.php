<?php

namespace App\Http\Helpers;

use App\Enums\ApiError;
use Illuminate\Http\JsonResponse;

trait ResponseHelper
{
    /**
     * Trả về response thành công.
     *
     * @param mixed|null $data Dữ liệu trả về
     * @param string $message Thông điệp
     * @param int $statusCode Mã HTTP
     * @return JsonResponse
     */
    public function success(mixed $data = null, string $message = 'Thành công', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Trả về response lỗi.
     *
     * @param ApiError $apiError Enum lỗi
     * @param mixed|null $errors Chi tiết lỗi (ví dụ từ Validator)
     * @return JsonResponse
     */
    public function error(ApiError $apiError, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'error_code' => $apiError->value,
            'message' => $apiError->getMessage(),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $apiError->getHttpCode());
    }
}