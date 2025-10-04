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
    
    public function findByName(Request $request){
        $name = $request->query('name'); 

        if (!$name) {
            return $this->error(ApiError::VALIDATION_FAILED, ['name' => 'The name field is required.']);
        }

        $users = $this->userService->findByName($name);
        return $this->success(UserResource::collection($users), ApiSuccess::GET_DATA_SUCCESS);
    }

    public function updateUser(Request $request, string $email){
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|digits:10',
            'role' =>  'sometimes|string',
        ]);

        if ($validator->fails()) {
            return $this->error(ApiError::VALIDATION_FAILED, $validator->errors());
        }

        $result = $this->userService->updateUser($email, $validator->validated());

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