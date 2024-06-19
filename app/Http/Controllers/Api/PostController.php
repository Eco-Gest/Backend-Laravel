<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\PostService;
use App\Services\TagService;
use App\Models\Category;
use App\Services\UserPointService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\UserPointCategory;
use App\Services\UserService;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{

    protected UserPointService $userPointService;
    protected TagService $tagService;

    protected PostService $postService;
    protected UserService $userService;
    public function __construct(UserPointService $userPointService, TagService $tagService, PostService $postService, UserService $userService)
    {
        $this->userPointService = $userPointService;
        $this->tagService = $tagService;
        $this->postService = $postService;
        $this->userService = $userService;
    }

    /**
     * Display a listing of posts using redis cache
     */
    public function index()
    {

        $posts = Post::orderBy('created_at', 'DESC')->paginate(8);

        $postsOfUserCommunity = [];

        foreach ($posts as $post) {
            if ($this->userService->checkIfCanAccessToResource($post->author_id) && $this->userService->isUserUnblocked($post->author_id)) {
                foreach ($post->userPostParticipation as $userPostParticipation) {
                    $userPostParticipation->users;
                }
                $post = $this->postService->loadPostData($post);
                $postsOfUserCommunity[] = $post;
            }
        }


        return response()->json($postsOfUserCommunity);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $this->userService->getUser();

        $validated = $request->validate([
            'category_id' => 'required|integer',
            "tags" => "nullable|array",
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'position' => 'nullable|string|max:255',
            'type' => 'required|string|max:255',
            'level' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $category = Category::where('id', $request['category_id'])->first();
        if (!$category) {
            return response()->json(['error' => 'Category not found.'], 404);
        }

        if ($validated['type'] == 'challenge') {
            if ($request['start_date'] == null || $request['end_date'] == null) {
                return response()->json(['error' => 'Start date or end date can not be null.'], 400);
            }
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images');
            $validated['image'] = $path;
        }

        $validated['author_id'] = $user->id;
        $validated['category_id'] = $category->id;

        $post = Post::create($validated);
        $this->postService->addAuthorPostToUserPostParticipation($post);

        if ($post->type != 'challenge') {
            $userPointCategory = UserPointCategory::where(['user_id' => $user->id, 'category_id' => $category->id])->first();
            $this->userPointService->updateUserCurrentPointCategory($post, $userPointCategory);
            $this->userPointService->setNewBadge($user);
        }


        $post->save();

        // If user adds tags
        if (isset($validated['tags'])) {
            $tagsToAttach = $this->tagService->addTagsToPost($validated['tags']);
            foreach ($tagsToAttach as $tagId) {
                $post->tags()->attach($tagId);
            }
        }

        return response()->json($post);
    }


    /**
     * Display the post by id using cache.
     */
    public function show(int $id)
    {
        if (Cache::has('post_' . $id)) {
            $post =  Cache::get('post_'. $id);
            if ($this->userService->checkIfCanAccessToResource($post->author_id) && $this->userService->isUserUnblocked($post->author_id)) {
                return response()->json($post);
            }
        }
        $post = Post::where('id', $id)->firstOrFail();
        if (!$this->userService->checkIfCanAccessToResource($post->author_id) || !$this->userService->isUserUnblocked($post->author_id)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        if (!$post) {
            return response()->json(['error' => 'Post not found.'], 404);
        }

        foreach ($post->userPostParticipation as $userPostParticipation) {
            $userPostParticipation->users;
        }


        $post = $this->postService->loadPostData($post);


        if ($this->userService->checkIfCanAccessToResource($post->author_id) && $this->userService->isUserUnblocked($post->author_id)) {
            Cache::put('post_' . $id, $post, 120);
        }

        return response()->json($post);
    }

    /**
     * Update the post in storage.
     * Remove cache by key if exists.
     */
    public function update(Request $request, int $id)
    {
        $user = $this->userService->getUser();

        $post = Post::where('id', $id)->firstOrFail();

        // check if there is no participants to allow the update
        if($post->userPostParticipation()->count() > 1){
            return response()->json(['error' => 'You can not update a post with participants.'], 409);
        }

        $validated = $request->validate([
            'category_id' => 'integer',
            "tags" => "nullable|array",
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'type' => 'string|max:255',
            'level' => 'string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        if (isset($validated['type']) == 'challenge') {
            if ($request['start_date'] == null || $request['end_date'] == null) {
                return response()->json(['error' => 'Start date or end date can not be null.'], 400);
            }
        }

        $category = Category::where('id', $validated['category_id'])->firstOrFail();

        if ($post->type != 'challenge') {
            $userPointCategory = UserPointCategory::where(['user_id' => $user->id, 'category_id' => $category->id])->first();
            $this->userPointService->updateUserCurrentPointCategoryPostUpdated($post, $validated, $userPointCategory);
            $this->userPointService->setNewBadge($user);
        }

        if (isset($validated['tags'])) {
            $post = $this->tagService->updateTagsToPost($post, $validated['tags']);
        }
        $post->update($validated);

        if (Cache::has('post_' . $id)) {
            Cache::forget('post_' . $id);
        }

        return response()->json($post);
    }

    /**
     * Remove the post by id
     * Remove cache by key if exists.
     */
    public function destroy(int $id)
    {
        $post = Post::where('id', $id)->firstOrFail();

        if (Cache::has('post_' . $id)) {
            Cache::forget('post_' . $id);
        }

        $post->delete();
    }

    public function getPostsByTag(string $tag)
    {
        $this->userService->getUser();

        $posts = $this->postService->getPostsByTag($tag);

        foreach ($posts as $post) {
            $post = $this->postService->loadPostData($post);
        }

        if ($posts == null) {
            return response()->json(['error' => 'Tag not found.'], 404);
        }
        return response()->json($posts);
    }
}
