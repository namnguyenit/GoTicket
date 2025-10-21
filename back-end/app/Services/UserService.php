<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Enums\ApiError;
use App\Models\Vendor; 
use Illuminate\Support\Facades\DB;

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
    public function findByName(string $name, ?string $role = null)
    {
        return $this->userRepository->findByName($name, $role);
    }

    public function findByEmail(string $email){
        $user = $this->userRepository->findByEmail($email);
        return $user;
    }

    public function update(User $user, array $data): bool
    {
        // ✅ 1. TẠO MẢNG DỮ LIỆU CHỈ BAO GỒM CÁC TRƯỜNG CỦA USER
        $userData = collect($data)->only(['name', 'phone_number', 'role'])->toArray();
        
        if (empty($userData)) {
            return true; // Không có gì để cập nhật
        }

        return DB::transaction(function () use ($user, $data, $userData) { 
            $originalRole = $user->role;
            
            // 2. CẬP NHẬT USER (Gọi Repository chỉ với dữ liệu User đã lọc)
            $updated = $this->userRepository->update($user, $userData); 

            // 3. LOGIC TẠO VENDOR KHI CHUYỂN ROLE (Giữ nguyên logic này, sử dụng $data ban đầu)
            if ($updated && $originalRole !== 'vendor' && ($data['role'] ?? null) === 'vendor') {
                $user->load('vendor'); 
                
                if ($user->vendor === null) { 
                    Vendor::create([
                        'user_id' => $user->id,
                        'company_name' => $data['company_name'] ?? $user->name . ' Mới', 
                        'address' => $data['address'] ?? 'Địa chỉ Mặc Định',
                        'status' => 'pending',
                    ]);
                }
            }
            
            return $updated;
        });
    }


    public function createVendor(array $data): User
    {
        // Tách dữ liệu thành 2 phần: User và Vendor
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password' => $data['password'],
        ];

        $vendorData = [
            'company_name' => $data['company_name'],
            'address' => $data['address'],
        ];

        return $this->userRepository->createVendor($userData, $vendorData);
    }
}