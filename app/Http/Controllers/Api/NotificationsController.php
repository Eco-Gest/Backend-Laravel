<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use App\Services\UserService;

class NotificationsController extends Controller
{
    
    protected UserService $userService;
    public function __construct(UserService $userService)
    {
      $this->userService = $userService;
    }

    public function index()
    {
        $userAuth = $this->userService->getUser();

        $notifications = [];
        foreach ($userAuth->notifications as $notification) {
            $notif = [];
            if (isset($notification->data["like_id"])) {
                $like = Like::where('id', $notification->data["like_id"])->firstOrFail();
                $notif["like"] = $like;
                $user = User::where('id', $like->user_id)->firstOrFail();
                $user->reward;
                $notif["user"] = $user;
                $post = Post::where("id", $like->post_id)->firstOrFail();
                $notif["post"] = $post;
                $notif["title"] = $user->username . " a liké votre publication !";
                $notif["notification"] = $notification;
            }
            if (isset($notification->data["comment_id"])) {
                $comment = Comment::where('id', $notification->data["comment_id"])->firstOrFail();
                $notif["comment"] = $comment;
                $user = User::where('id', $comment->author_id)->firstOrFail();
                $user->reward;
                $notif["user"] = $user;
                $post = Post::where("id", $comment->post_id)->firstOrFail();
                $notif["post"] = $post;
                $notif["title"] = $user->username . " a commenté votre publication !";
                $notif["notification"] = $notification;
            }
            if (isset($notification->data["subscription_id"])) {
                $subscription = Subscription::where('id', $notification->data["subscription_id"])->firstOrFail();
                $notif["subscription"] = $subscription;
                $user = User::where('id', $subscription->follower_id)->firstOrFail();
                $user->reward;
                $notif["user"] = $user;
                if ($subscription->status == "pending") {
                    $notif["title"] = $user->username . " a demandé à vous suivre !";
                } else if ($subscription->status == "approved") {
                    $notif["title"] = $user->username . " a accepté votre demande d'invitation !";
                }
                $notif["notification"] = $notification;
            }
            $notifications[] = $notif;
        }
        return response()->json($notifications);
    }


}