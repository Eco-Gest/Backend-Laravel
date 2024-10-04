<?php

namespace App\Policies;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{

    use HandlesAuthorization;

    /**
     * Determine whether the user can update the informations linked to the user profile.
     */
    public function update(User $authUser, User $user): bool
    {
        return $authUser->id === $user->id;
    }
    

    /**
     * Determine whether the user can delete the user profile.
     */
    public function delete(User $authUser, User $user): bool
    {
        return $authUser->id === $user->id;
    }    

}
