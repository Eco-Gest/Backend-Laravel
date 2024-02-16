<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;

class UserService
{
    public function getUser() : User
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
            $user->badge;
            $user->userTrophy;
            $user->userPostParticipation;
        }

        return $users;
    }

    public function checkIfCanAccessToRessource($authorId): bool
    {
        $author = User::where("id", $authorId)->firstOrFail();
        $userAuthenticated = auth()->user();

        if (!$userAuthenticated || !$author) {
            return response()->json(['error' => 'User not found.'], 404);
        }
        if ($author->is_private) {
            $userAuthenticatedFollowing = Subscription::where(['status' => 'approved', 'following_id' => $author->id, 'follower_id' => $userAuthenticated->id]);
            if ($userAuthenticatedFollowing->count() < 1 && $author->id != $userAuthenticated->id) {
                return false;
            }
        }
        return true;
    }
}