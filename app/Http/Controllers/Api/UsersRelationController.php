<?php

namespace App\Http\Controllers\Api;

use App\Events\SubscriptionEvent;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Notifications\UserSubscribed;
use Illuminate\Http\Request;
use App\Services\UserService;

use App\Models\UsersRelation;

class UsersRelationController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * subscribe to a user
     */
    public function subscribe(int $userId)
    {
        $userAuthenticated = $this->userService->getUser();
        if (!$userAuthenticated) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $userAlreadySubscribed = UsersRelation::where(['follower_id' => $userAuthenticated->id, 'following_id' => $userId])
            ->where(function ($query) {
                $query->where('status', 'approved')->orWhere('status', 'pending');
            });

        if ($userAlreadySubscribed->count() > 0) {
            return response()->json(['error' => 'User subcription is already approved or is pending.'], 400);
        }

        if ($userAuthenticated->id == $userId) {
            return response()->json(['error' => 'Impossible to subscribe to yourself'], 400);
        }

        $subscription = UsersRelation::create([
            'follower_id' => $userAuthenticated->id,
            'following_id' => $userId,
            'status' => 'pending',
        ]);
        $user = User::where('id', $userId)->first();
        $user->notify(new UserSubscribed($subscription, $userAuthenticated));
        event(new SubscriptionEvent($subscription));

        $subscription->save();
        return response()->json($subscription);
    }


    /**
     * Unsubscribe to a user.
     */

    public function unSubscribe(int $userId)
    {
        $userAuthenticated = $this->userService->getUser();

        if (!$userAuthenticated) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $userSubscriptionExists = UsersRelation::where(['follower_id' => $userAuthenticated->id, 'following_id' => $userId, 'status' => 'approved']);
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
        $userAuthenticated = $this->userService->getUser();

        if (!$userAuthenticated) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $subscription = UsersRelation::where(['following_id' => $userAuthenticated->id, 'follower_id' => $userId, 'status' => 'pending'])->firstOrFail();
        if ($subscription->count() == 0) {
            return response()->json(['error' => 'Subscription request not found.'], 404);
        }

        $user = User::where('id', $userAuthenticated->id)->first();

        $subscription->status = 'approved';
        $subscription->save();

        $userAuthenticated->notify(new UserSubscribed($subscription, $userAuthenticated));
        event(new SubscriptionEvent($subscription));

        return response()->json($subscription);
    }

    /**
     * Cancel a subscription
     */

    public function cancelSubscriptionRequest(int $userId)
    {
        $userAuthenticated = $this->userService->getUser();

        if (!$userAuthenticated) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $subscription = UsersRelation::where(['follower_id' => $userAuthenticated->id, 'following_id' => $userId, 'status' => 'pending'])->firstOrFail();
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
        $userAuthenticated = $this->userService->getUser();

        if (!$userAuthenticated) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $subscription = UsersRelation::where(['following_id' => $userAuthenticated->id, 'follower_id' => $userId, 'status' => 'pending']);
        if ($subscription->count() == 0) {
            return response()->json(['error' => 'Subscription request not found.'], 404);
        }
        $subscription->delete();
        return response()->json('Subscription request declined');
    }
    public function removeFollower(int $userId)
    {
        $userAuthenticated = $this->userService->getUser();

        $subscription = UsersRelation::where(['following_id' => $userAuthenticated->id, 'follower_id' => $userId, 'status' => 'approved'])->firstOrFail();
        $subscription->delete();
        return response()->json('Followed removed');
    }

    /**
     * Block a user
     * @param int $userId
     */
    public function blockUser(int $userId)
    {
        $userAuthenticated = $this->userService->getUser();

        $subscription = UsersRelation::where(['follower_id' => $userAuthenticated->id, 'following_id' => $userId])->first();
        if ($subscription == null) {
            $subscription = UsersRelation::create([
                'follower_id' => $userAuthenticated->id,
                'following_id' => $userId,
                'status' => 'blocked',
            ]);
        } else {
            $subscription->status = 'blocked';
        }

        $followerSubscription = UsersRelation::where(['following_id' => $userAuthenticated->id, 'follower_id' => $userId])->first();

        if ($followerSubscription != null) {
            $followerSubscription->delete();
        }
        
        $subscription->save();
        return response()->json('User blocked successfully');
    }


    /**
     * unBlock a user
     * @param int $userId
     */
    public function unblockUser(int $userId)
    {
        $userAuthenticated = $this->userService->getUser();

        $subscription = UsersRelation::where(['follower_id' => $userAuthenticated->id, 'following_id' => $userId, 'status' => 'blocked'])->firstOrFail();
        $subscription->delete();

        return response()->json('User unblocked successfully');
    }
}