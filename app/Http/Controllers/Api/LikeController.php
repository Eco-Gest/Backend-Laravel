<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    /**
     * Like a post.
     */
    public function likePost(int $postId)  
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $post = Post::where('id', $postId)->first();
        if (!$post) {
            return response()->json(['error' => 'Post not found.'], 404);
        }

        $likeAlreadyExists = Like::where(['post_id'=> $postId, 'user_id'=> $user->id]);
        if ($likeAlreadyExists->count() > 0) {
            return response()->json(['error' => 'Post already liked.'], 400);
        }

        $like = Like::create([
            'post_id' => $postId,
            'user_id' => $user->id,
        ]);

        $like->save();
        return response()->json($like);
    }


    /**
     * Unlike a post.
     */

    public function unlikePost(int $postId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $post = Post::where('id', $postId)->first();
        if (!$post) {
            return response()->json(['error' => 'Post not found.'], 404);
        }

        $like = Like::where(['post_id'=> $postId, 'user_id'=> $user->id]);
        if ($like->count() < 1) {
            return response()->json(['error' => 'Post not liked.'], 400);
        }

  
        $like->delete();
        return response()->json('like deleted');
    }

}