<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get paginated list of users with sorting
     *
     * @param int $perPage
     * @param string $sortBy
     * @param string $sortDirection
     * @return LengthAwarePaginator
     */
    public function getPaginatedUsers(int $perPage = 10, string $sortBy = 'name', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        return User::orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    /**
     * Create a new user
     *
     * @param array $userData
     * @return User
     */
    public function createUser(array $userData): User
    {
        // Handle password hashing
        if (isset($userData['password'])) {
            $userData['password'] = bcrypt($userData['password']);
        }

        return User::create($userData);
    }

    /**
     * Update existing user
     *
     * @param User $user
     * @param array $userData
     * @return User
     */
    public function updateUser(User $user, array $userData): User
    {
        // Only hash password if it's being updated
        if (isset($userData['password'])) {
            $userData['password'] = bcrypt($userData['password']);
        }

        $user->update($userData);
        return $user->fresh();
    }

    /**
     * Delete a user
     *
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }
} 