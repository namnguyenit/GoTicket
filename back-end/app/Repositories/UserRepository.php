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
        }

        return $query->get();
    }


    public function findByName(string $name)
    {
        return User::where('name', 'LIKE', "%$name%")->get();
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