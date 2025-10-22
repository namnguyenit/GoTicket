<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiError;
use App\Enums\ApiSuccess;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    use ResponseHelper;
    
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function getAll(){
         $users = $this->userService->getAll();
         return $this->success(UserResource::collection($users), ApiSuccess::GET_DATA_SUCCESS);
    }

    public function findByEmail(string $email){
        $user = $this->userService->findByEmail($email);

        if (!$user) {
            return $this->error(ApiError::NOT_FOUND);
        }
        return $this->success(new UserResource($user), ApiSuccess::GET_DATA_SUCCESS);
    }
    
    public function findByName(\App\Http\Requests\Api\FindUserByNameRequest $request){
        $validated = $request->validated();
        $users = $this->userService->findByName($validated['name']);
        return $this->success(UserResource::collection($users), ApiSuccess::GET_DATA_SUCCESS);
    }

    public function updateUser(\App\Http\Requests\Api\UpdateUserRequest $request, string $email){
        $validated = $request->validated();
        $result = $this->userService->updateUser($email, $validated);

        if (!$result) {
            return $this->error(ApiError::NOT_FOUND);
        }
        return $this->success(null, ApiSuccess::ACTION_SUCCESS);
    }
    
    public function delete(string $email){
        $result = $this->userService->delete($email);

        if (!$result) {
            return $this->error(ApiError::NOT_FOUND);
        }

        return $this->success(null, ApiSuccess::ACTION_SUCCESS);
    }
}