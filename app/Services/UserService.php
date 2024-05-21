<?php

namespace App\Services;

use App\Models\UsersRelation;
use App\Models\User;

class UserService
{
    public function getUser(): User
    {
        $user = auth()->user();
        $user = User::where('id', $user->id)->firstOrFail();

        return $user;
    }

    public function searchByUsernameOrEmail(string $q)
    {
        // Participant lists with details
        $users = User::where('username', 'ILIKE', '%' . $q . '%')
            ->orWhere('email', 'ILIKE', '%' . $q . '%')
            ->take(10)
            ->get();
        foreach ($users as $user) {
            if (!$this->checkIfCanAccessToRessource($user->id)) {
                $user->userTrophy = [];
                $user->userPostParticipation = [];
                $user->follower = [];
                $user->following = [];
            } else if (!$this->isUserUnblocked($user->id)) {
                $user->userTrophy = [];
                $user->userPostParticipation = [];
                $user->follower->load('follower')->where('follower_id', $user->id)->first();
                $user->following = [];
            } else {
                $user->userTrophy;
                $user->userPostParticipation;
                $user->follower->load('follower');
                $user->following->load('following');
            }
            $user->badge;
        }

        return $users;
    }

    public function checkIfCanAccessToRessource($authorId): bool
    {
        if ($authorId == null)
            return true;
        $author = User::where("id", $authorId)->firstOrFail();
        $userAuthenticated = auth()->user();

        if (!$userAuthenticated || !$author) {
            return response()->json(['error' => 'User not found.'], 404);
        }
        if ($author->is_private) {
            $userAuthenticatedFollowing = UsersRelation::where(['status' => 'approved', 'following_id' => $author->id, 'follower_id' => $userAuthenticated->id]);
            if ($userAuthenticatedFollowing->count() < 1 && $author->id != $userAuthenticated->id) {
                return false;
            }
        }
        return true;
    }

    public function isUserUnblocked($userId): bool
    {
        $userAuthenticated = auth()->user();
        return UsersRelation::where(['status' => 'blocked', 'following_id' => $userId, 'follower_id' => $userAuthenticated->id])->count() > 0 ? false : true;
    }
}