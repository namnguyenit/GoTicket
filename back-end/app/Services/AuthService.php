<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Enums\ApiError;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(array $data): User
    {
        $user = $this->userRepository->create($data);

        return $user;
    }

    public function loginUser(array $credentials): array
    {

        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user) {

            return ['error' => ApiError::EMAIL_NOT_EXISTS];
        }

        if (! $token = auth('api')->attempt($credentials)) {

            return ['error' => ApiError::WRONG_PASSWORD];
        }

        return ['token' => $token];
    }

    public function getMyAccount(): ?User
    {





        return auth('api')->user();
    }


    public function updateProfile(User $user, array $data): bool
    {

        $updateData = collect($data)->only(['name', 'phone_number'])->all();

        if (!empty($data['password'])) {

            if (!Hash::check($data['current_password'], $user->password)) {

                throw ValidationException::withMessages([
                    'current_password' => 'Mật khẩu hiện tại không chính xác.',
                ]);
            }

            $updateData['password'] = Hash::make($data['password']);
        }

        return $user->update($updateData);
    }
}