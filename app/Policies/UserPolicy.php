<?php

namespace App\Policies;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{

    use HandlesAuthorization;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Determine whether the user can view the informations linked to the user.
     */
    public function view($post, $user): bool
    {
        return $this->userService->checkIfCanAccessToResource($user->id) && $this->userService->isUserUnblocked($user->id); 
    }

    /**
     * Determine whether the user can update the informations linked to the user profile.
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
