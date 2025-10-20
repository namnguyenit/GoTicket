<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Enums\ApiError;


class UserService{
    
    protected $userRepository;

    // "Tiêm" UserRepository vào đây
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    
    
    public function getAll(?string $role = null){
        $alluser  = $this->userRepository->all($role);
        return $alluser; 
    }

    public function delete(string $email){
        $result = $this->userRepository->delete($email);
        return $result;
    }

    // Bổ sung tham số $name
    public function findByName(string $name){
        $alluser = $this->userRepository->findByName($name);
        return $alluser;
    }

    public function findByEmail(string $email){
        $user = $this->userRepository->findByEmail($email);
        return $user;
    }

    public function updateUser(string $email ,array $data){
        // Lọc ra các giá trị null để không cập nhật các trường không được gửi lên
        $updateData = array_filter($data, function ($value) {
            return !is_null($value);
        });

        if (empty($updateData)) {
            return false;
        }
        
        $result = $this->userRepository->update($email, $updateData);
        return $result;
    }
}