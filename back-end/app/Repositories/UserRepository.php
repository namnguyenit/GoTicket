<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'phone_number' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function all(?string $role = null): Collection
    {
        $query = User::query();

        // Nếu có tham số 'role' được truyền vào, thêm điều kiện lọc
        if ($role) {
            $query->where('role', $role);

            // ĐIỀU KIỆN ĐẶC BIỆT: Nếu là 'vendor', lấy kèm thông tin vendor
            if ($role === 'vendor') {
                // Eager load the 'vendor' relationship
                $query->with('vendor');
            }
        }

        return $query->get();
    }


    public function findByName(string $name, ?string $role = null): Collection
    {
        $query = User::query();

        // Nếu không có role hoặc role là 'customer', chỉ tìm theo tên user
        if (!$role || $role === 'customer') {
            $query->where('role', 'customer')
                  ->where('name', 'LIKE', $name . '%');
        } 
        // Nếu role là 'vendor', tìm kiếm phức tạp hơn
        elseif ($role === 'vendor') {
            $query->where('role', 'vendor')
                  ->with('vendor') // Lấy kèm thông tin vendor
                  // Điều kiện: Hoặc tên user khớp, HOẶC tên công ty của vendor khớp
                  ->where(function ($q) use ($name) {
                      $q->where('name', 'LIKE', $name . '%')
                        ->orWhereHas('vendor', function ($subQ) use ($name) {
                            $subQ->where('company_name', 'LIKE', $name . '%');
                        });
                  });
        }

        return $query->take(10)->get();
    }


    public function delete(string $email): bool
    {
        $user = $this->findByEmail($email);
        if ($user) {
            return $user->delete(); 
        }
        return false;
    }


    public function update(string $email, array $data): bool
    {
        $user = $this->findByEmail($email);
        if ($user) {
            return $user->update($data); 
        }
        return false;
    }
}