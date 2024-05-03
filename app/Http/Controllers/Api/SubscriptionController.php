<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Notifications\UserSubscribed;
use Illuminate\Http\Request;

use App\Models\Subscription;

class SubscriptionController extends Controller
{
    /**
     * subscribe to a user
     */
    public function subscribe(int $userId)
    {
        $userAuthenticated = auth()->user();
        if (!$userAuthenticated) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $userAlreadySubscribed = Subscription::where(['follower_id' => $userAuthenticated->id, 'following_id' => $userId])
            ->where(function ($query) {
                $query->where('status', 'approved')->orWhere('status', 'pending');
            });

        if ($userAlreadySubscribed->count() > 0) {
            return response()->json(['error' => 'User subcription is already approved or is pending.'], 400);
        }

        if ($userAuthenticated->id == $userId) {
            return response()->json(['error' => 'Impossible to subscribe to yourself'], 400);
        }

        $subscription = Subscription::create([
            'follower_id' => $userAuthenticated->id,
            'following_id' => $userId,
            'status' => 'pending',
        ]);
        $user = User::where('id', $userId)->first();
        $user->notify(new UserSubscribed($subscription));

        $subscription->save();
        return response()->json($subscription);
    }


    /**
     * Unsubscribe to a user.
     */

    public function unSubscribe(int $userId)
    {
        $userAuthenticated = auth()->user();
        if (!$userAuthenticated) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $userSubscriptionExists = Subscription::where(['follower_id' => $userAuthenticated->id, 'following_id' => $userId, 'status' => 'approved']);
        if ($userSubscriptionExists->count() == 0) {
            return response()->json(['error' => 'Subscription not found.'], 404);
        }

        $userSubscriptionExists->delete();
        return response()->json('User unfollowed');
    }

    /**
     * Accept a subscription
     */

    public function acceptSubscriptionRequest(int $userId)
    {
        $userAuthenticated = auth()->user();
        if (!$userAuthenticated) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $subscription = Subscription::where(['following_id' => $userAuthenticated->id, 'follower_id' => $userId, 'status' => 'pending'])->firstOrFail();
        if ($subscription->count() == 0) {
            return response()->json(['error' => 'Subscription request not found.'], 404);
        }

        $user = User::where('id', $userAuthenticated->id)->first();
        $user->notify(new UserSubscribed($subscription));

        $subscription->status = 'approved';
        $subscription->save();
        return response()->json($subscription);
    }

    /**
     * Cancel a subscription
     */

    public function cancelSubscriptionRequest(int $userId)
    {
        $userAuthenticated = auth()->user();
        if (!$userAuthenticated) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $subscription = Subscription::where(['follower_id' => $userAuthenticated->id, 'following_id' => $userId, 'status' => 'pending'])->firstOrFail();
        if ($subscription->count() == 0) {
            return response()->json(['error' => 'Subscription request not found.'], 404);
        }

        $subscription->delete();
        return response()->json('Subscription request canceled');
    }


    /**
     * Accept a subscription
     */

    public function declineSubscriptionRequest(int $userId)
    {
        $userAuthenticated = auth()->user();
        if (!$userAuthenticated) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $subscription = Subscription::where(['following_id' => $userAuthenticated->id, 'follower_id' => $userId, 'status' => 'pending']);
        if ($subscription->count() == 0) {
            return response()->json(['error' => 'Subscription request not found.'], 404);
        }
        $subscription->delete();
        return response()->json('Subscription request declined');
    }
    public function removeFollower(int $userId)
    {
        $userAuthenticated = $this->userService->getUser();

        $subscription = Subscription::where(['following_id' => $userAuthenticated->id, 'follower_id' => $userId, 'status' => 'approved'])->firstOrFail();
        $subscription->delete();
        return response()->json('Followed removed');
    }
}