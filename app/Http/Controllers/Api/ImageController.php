<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use App\Services\UserService;

class ImageController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function uploadImagePost(Request $request, int $postId)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        $post = Post::where('id', $postId)->firstOrFail();

        // delete old image
        if ($post->image) {
            Storage::delete($post->image);
        }

        $post->update(['image' => $request->file('image')->store('public/posts')]);
    }

    public function uploadImageUser(Request $request, int $userId)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        $user = User::where('id', $userId)->firstOrFail();

        // delete old image
        if ($user->image) {
            Storage::delete($user->image);
        }
        $user->update(['image' => $request->file('image')->store('public/users')]);
    }

    public function getImage($path)
    {
        return response()->file(storage_path() . '/app/' . $path);
    }
}