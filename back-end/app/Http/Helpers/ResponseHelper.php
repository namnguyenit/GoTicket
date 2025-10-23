<?php

namespace App\Http\Helpers;

use App\Enums\ApiError;
use App\Enums\ApiSuccess; 
use Illuminate\Http\JsonResponse;

trait ResponseHelper
{
    public function success(mixed $data = null, ApiSuccess $apiSuccess = ApiSuccess::ACTION_SUCCESS): JsonResponse
    {
        $httpCode = $apiSuccess->getHttpCode(); 
        $response = [
            'success' => true,
            'status' => $httpCode, 
            'message' => $apiSuccess->getMessage(),
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $httpCode);
    }

    public function error(ApiError $apiError, mixed $errors = null): JsonResponse
    {
        $httpCode = $apiError->getHttpCode(); 
        $response = [
            'success' => false,
            'status' => $httpCode, 
            'error_code' => $apiError->value,
            'message' => $apiError->getMessage(),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $httpCode);
    }
}