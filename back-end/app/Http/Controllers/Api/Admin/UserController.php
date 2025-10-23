<?php

namespace App\Http\Controllers\Api\Admin; 

use App\Enums\ApiError;
use App\Enums\ApiSuccess;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Requests\Api\Admin\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    use ResponseHelper;
    
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function getAll(Request $request){
        
        $validated = $request->validate([
            'role' => ['nullable', 'string', Rule::in(['customer', 'vendor', 'admin'])]
        ]);
        
        $role = $validated['role'] ?? null;
         
        $users = $this->userService->getAll($role);
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
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            // Role không bắt buộc, nếu có phải là các giá trị này
            'role' => ['nullable', 'string', Rule::in(['customer', 'vendor'])]
        ]);

        $name = $validated['name'];
        $role = $validated['role'] ?? null; 

        $users = $this->userService->findByName($name, $role);
        
        
        return $this->success(UserResource::collection($users), ApiSuccess::GET_DATA_SUCCESS);
    }

    public function update(Request $request, string $email) 
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|digits:10',
            'role' => ['required', 'string', Rule::in(['customer', 'vendor', 'admin'])],

            'company_name' => 'nullable|string|max:255', 
            'address' => 'nullable|string|max:255',
        ]);


        $user = $this->userService->findByEmail($email);
        if (!$user) {
            return $this->error(ApiError::NOT_FOUND, ['message' => 'Không tìm thấy người dùng để cập nhật.']);
        }
        

        $dataToUpdate = $validated;

        $dataToUpdate['company_name'] = $request->input('company_name'); 
        $dataToUpdate['address'] = $request->input('address'); 
        

        $this->userService->update($user, $dataToUpdate);


        $user->load('vendor'); 

        return $this->success(new UserResource($user), ApiSuccess::ACTION_SUCCESS);
    }
    
    public function delete(string $email){
        $result = $this->userService->delete($email);

        if (!$result) {
            return $this->error(ApiError::NOT_FOUND);
        }

        return $this->success(null, ApiSuccess::ACTION_SUCCESS);
    }
}