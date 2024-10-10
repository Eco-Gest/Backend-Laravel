<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Determine whether the user can view a post.
     */
    public function view(User $user, Post $post): bool
    {
        return $this->userService->checkIfCanAccessToResource($post->author_id) &&
               $this->userService->isUserUnblocked($post->author_id);
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user): bool
    {
        // all authentificated user can create posts
                return true; 
    }

    /**
     * Determine whether the user can update a post.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->author_id;
    }

    /**
     * Determine whether the user can delete a post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->author_id;
    }

}
