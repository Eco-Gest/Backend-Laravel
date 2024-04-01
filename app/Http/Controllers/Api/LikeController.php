<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use App\Notifications\PostLiked;
use App\Services\UserService;
use App\Events\LikeEvent;

class LikeController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Like a post.
     */
    public function likePost(int $postId)
    {
        $user = $this->userService->getUser();

        $post = Post::where('id', $postId)->first();
        if (!$post) {
            return response()->json(['error' => 'Post not found.'], 404);
        }

        $likeAlreadyExists = Like::where(['post_id' => $postId, 'user_id' => $user->id]);
        if ($likeAlreadyExists->count() > 0) {
            return response()->json(['error' => 'Post already liked.'], 400);
        }

        $like = Like::create([
            'post_id' => $postId,
            'user_id' => $user->id,
        ]);

        $like->save();


        if ($post->author_id != $user->id) {
            $post->user->notify(new PostLiked($like));
            event(new LikeEvent($user, $post, $like));
        }

        return response()->json($like);
    }


    /**
     * Unlike a post.
     */

    public function unlikePost(int $postId)
    {
        $user = $this->userService->getUser();

        $post = Post::where('id', $postId)->first();
        if (!$post) {
            return response()->json(['error' => 'Post not found.'], 404);
        }

        $like = Like::where(['post_id' => $postId, 'user_id' => $user->id]);
        if ($like->count() < 1) {
            return response()->json(['error' => 'Post not liked.'], 400);
        }


        $like->delete();
        return response()->json('like deleted');
    }

}