<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiError;
use App\Enums\ApiSuccess;
use App\Http\Requests\Api\LoginRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Api\UpdateProfileRequest;

class AuthController extends Controller
{
    use ResponseHelper;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterUserRequest $request)
    {

        $validatedData = $request->validated();

        $user = $this->authService->registerUser($validatedData);

        

        return $this->success([
            'user' => new UserResource($user)
            
        ], ApiSuccess::CREATED_SUCCESS);
    }


    public function login(LoginRequest $request)
{
    $credentials = $request->validated();

    Log::info('validate :' , $credentials);
    $result = $this->authService->loginUser($credentials);

    

    if (isset($result['error'])) {

        return $this->error($result['error']);
    }

    return $this->success([
        'authorisation' => [
            'token' => $result['token'],
            'type' => 'bearer',
        ]
    ], ApiSuccess::ACTION_SUCCESS);
}
    public function getInfoAccout()
    {

        $user = $this->authService->getMyAccount();

        if (!$user) {
            return $this->error(ApiError::UNAUTHORIZED);
        }

        return $this->success(
            new UserResource($user),
            ApiSuccess::GET_DATA_SUCCESS
        );
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth('api')->user();
        $validatedData = $request->validated();

        try {

            $this->authService->updateProfile($user, $validatedData);

            return $this->success(new UserResource($user), ApiSuccess::ACTION_SUCCESS);

        } catch (ValidationException $e) {


            return $this->error(ApiError::VALIDATION_FAILED, $e->errors());
        }
    }
}