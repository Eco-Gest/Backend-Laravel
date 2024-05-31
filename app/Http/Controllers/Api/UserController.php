<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserPointService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
  protected UserPointService $userPointService;
  protected UserService $userService;
  public function __construct(UserPointService $userPointService, UserService $userService)
  {
    $this->userPointService = $userPointService;
    $this->userService = $userService;
  }

  public function getUserData()
  {
    $user = $this->userService->getUser();

    $user->badge;
    $user->userTrophy;
    $user->userPostParticipation;
    $user->follower->load('follower');
    $user->following->load('following');
    $user->total_point = $this->userPointService->userTotalPoints($user->id);

    return response()->json($user);
  }

  public function show(int $userId)
  {
    if (Cache::has('user_' . $userId)) {
      return response()->json(Cache::get('post_' . $userId));
    }
    $res = Cache::remember('post_' . $userId, 60, function () use ($userId) {
      $user = User::findOrFail($userId);

      $user->badge;
      $user->total_point = $this->userPointService->userTotalPoints($user->id);

      if (!$this->userService->checkIfCanAccessToRessource($user->id)) {
        $user->userTrophy = [];
        $user->userPostParticipation = [];
        $user->follower = [];
        $user->following = [];
      } else if (!$this->userService->isUserUnblocked($user->id)) {
        $user->userTrophy = [];
        $user->userPostParticipation = [];
        $user->follower->load('follower')->where('follower_id', $user->id)->first();
        $user->following = [];
      } else {
        $user->userTrophy;
        $user->userPostParticipation;
        $user->follower->load('follower');
        $user->following->load('following');
      }
      return $user;
    });

    return response()->json($res);
  }

  /**
   * Update the user data.
   * Remove cache by key if exists.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {
    $user = $this->userService->getUser();

    $validated = $request->validate([
      'email' => 'nullable|string|email',
      'username' => 'nullable|string|max:255',
      'badge_id' => 'nullable|integer',
      'birthdate' => 'nullable|date',
      'biography' => 'nullable|string',
      'position' => 'nullable|string|max:255',
      "is_private" => 'nullable|boolean'
    ]);

    $user->update($validated);

    if (Cache::has('user' . $user->id)) {
      Cache::forget('user_' . $user->id);
    }

    return response()->json($user);

  }


  /**
   * Remove the user by id
   * Remove cache by key if exists.
   */
  public function destroy()
  {
    $user = $this->userService->getUser();
    $user->deleteUserActionsPosts($user->id);

    if (Cache::has('user' . $user->id)) {
      Cache::forget('user_' . $user->id);
    }

    $user->delete();
  }
}