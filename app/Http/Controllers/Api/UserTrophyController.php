<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserTrophy;
use Illuminate\Http\Request;
use App\Services\UserService;

class UserTrophyController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Recover all trophies of a user.
     */
    public function index(int $userId)
    {
        $user = User::where('id', $userId)->firstOrFail();
        $userAuthenticated = auth()->user();

        if (!$userAuthenticated || !$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }
        if ($user->is_private) {
            $userAuthenticatedFollowing = Subscription::where(['status' => 'approved', 'following_id' => $user->id, 'follower_id' => $userAuthenticated->id]);
            if ($userAuthenticatedFollowing->count() < 1 && $userId != $userAuthenticated->id) {
                return response()->json(['error' => 'User private'], 400);
            }
        }

        $userTrophies = UserTrophy::where('user_id', $user->id)->get();

        if (!$userTrophies) {
            return response()->json(['error' => 'Trophy of user not found.'], 404);
        }

        foreach ($userTrophies as $userTrophy) {
            $userTrophy->categories;
        }
        return response()->json($userTrophies);
    }

    /**
     * Store a new trophy for user.
     */
    public function store(Request $request)
    {
        $user = $this->userService->getUser();

        $validated = $request->validate([
            'category_id' => 'required',
        ]);

        $category = Category::where('id', $request['category_id'])->first();
        if (!$category) {
            return response()->json(['error' => 'Category not found.'], 404);
        }

        $userTrophy = UserTrophy::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $userTrophy->save();
        return response()->json($userTrophy);
    }

    /**
     * Display a trophy of a user.
     */
    public function show(int $userId, int $categoryId)
    {
        $user = User::where('id', $userId)->firstOrFail();
        $userAuthenticated = auth()->user();

        if (!$userAuthenticated || !$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }
        if ($user->is_private) {
            $userAuthenticatedFollowing = Subscription::where(['status' => 'approved', 'following_id' => $user->id, 'follower_id' => $userAuthenticated->id]);
            if ($userAuthenticatedFollowing->count() < 1 && $userId != $userAuthenticated->id) {
                return response()->json(['error' => 'User private'], 400);
            }
        }

        $userTrophy = UserTrophy::where(['user_id' => $user->id, 'category_id' => $categoryId])->first();

        if (!$userTrophy) {
            return response()->json(['error' => 'Trophy not found.'], 404);
        }

        $userTrophy->categories;
        return response()->json($userTrophy);
    }

    /**
     * Update a trophy of a user.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $userTrophy = UserTrophy::where('user_id', $user->id)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $validated = $request->validate([
            'category_id' => 'nullable|integer',
        ]);

        $userTrophy->update($validated);

        return response()->json($userTrophy);
    }

    /**
     * Remove a trophy of a user.
     */
    public function destroy(int $id)
    {
        $userTrophy = UserTrophy::where('id', $id)->firstOrFail();

        $userTrophy->delete();
    }
}