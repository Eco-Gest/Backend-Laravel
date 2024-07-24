<?php

namespace App\Policies;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{

    use HandlesAuthorization;
    protected $userService;

    /**
     * Determine whether the user can view he user profile.
     */
    public function view($userId): bool
    {
        return $this->userService->checkIfCanAccessToResource($userId) && $this->userService->isUserUnblocked($userId);
    }

    /**
     * Determine whether the user can update the the user profile.
     */
    public function update($userId): bool
    {
        return $userId === $this->userService->getUser();
    }

    /**
     * Determine whether the user can delete the user profile.
     */
    public function delete($userId): bool
    {
        return $userId === $this->userService->getUser();
    }

}
