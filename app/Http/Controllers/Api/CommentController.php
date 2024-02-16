<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Notifications\PostCommented;
use App\Services\UserService;

class CommentController extends Controller
{

    protected UserService $userService;
    public function __construct(UserService $userService)
    {
      $this->userService = $userService;
    }

    /**
     * Comment a post.
     */
    public function store(Request $request, int $postId)
    {
        $user = $this->userService->getUser();
        $post = Post::where('id', $postId)->first();
        if (!$post) {
            return response()->json(['error' => 'Post not found.'], 404);
        }

        $validated = $request->validate([
            'content' => "required|string",
        ]);

        $comment = Comment::create([
            'post_id' => $postId,
            'author_id' => $user->id,
            'content' => $validated['content']
        ]);

        $comment->save();
        $post->user->notify(new PostCommented($comment));

        return response()->json($comment);
    }

    /**
     * Edit a comment from a post.
     */
    public function update(Request $request, int $id)
    {
        $user = auth()->user();


        if ($user === null) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        $comment = Comment::where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'content' => "required|string",
        ]);

        $comment->update($validated);
        return response()->json($comment);
    }


    /**
     * Remove a comment from a post.
     */
    public function destroy(int $id)
    {
        $comment = Comment::where('id', $id)->firstOrFail();
        $comment->delete();
    }

}