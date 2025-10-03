<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

    public function all()
    {
        return User::all();
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

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            return $user->update($data); 
        }
        return false;
    }
}