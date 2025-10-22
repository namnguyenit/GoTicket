<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

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

        
        if ($role) {
            $query->where('role', $role);


            if ($role === 'vendor') {

                $query->with('vendor');
            }
        }

        return $query->get();
    }


    public function findByName(string $name, ?string $role = null): Collection
    {
        $query = User::query();


        if (!$role || $role === 'customer') {
            $query->where('role', 'customer')
                  ->where('name', 'LIKE', $name . '%');
        } 

        elseif ($role === 'vendor') {
            $query->where('role', 'vendor')
                  ->with('vendor') 

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


    public function update(User $user, array $data): bool
    {

        if (empty($data)) {
            return true;
        }
        
        return $user->update($data);
    }

    public function createVendor(array $userData, array $vendorData): User
    {
        return DB::transaction(function () use ($userData, $vendorData) {

            $user = User::create([
                'name' => $userData['name'],
                'phone_number' => $userData['phone_number'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => 'vendor',
            ]);


            Vendor::create([
                'user_id' => $user->id,
                'company_name' => $vendorData['company_name'],
                'status' => 'pending', 
                'address' => $vendorData['address'],
            ]);

            return $user;
        });
    }
}