<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PostService;
use App\Services\UserService;
use App\Models\Post;
use App\Models\User;

class SearchController extends Controller
{
    protected PostService $postService;
    protected UserService $userService;

    public function __construct(PostService $postService, UserService $userService)
    {
        $this->postService = $postService;
        $this->userService = $userService;
    }
    public function getResult(string $q)
    {
        // Participants list with details
        $users = $this->userServicesearchByUsernameOrEmail($q);
        $result['users'] = $users;

        // Posts list with details
        $posts = $this->postService->searchByTitleOrDescriptionOrTag($q);
        $result['posts'] = $posts;

        return response()->json($result);
    }
}
