<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\UserPointCategory;
use App\Services\UserPointService;
use App\Services\UserService;
use Illuminate\Http\Request;

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
    $user->follower;
    $user->following;
    $user->total_point = $this->userPointService->userTotalPoints($user->id);

    return response()->json($user);
  }

  public function show(int $userId)
  {
    $user = $this->userService->getUser();

    $user->badge;
    $user->total_point = $this->userPointService->userTotalPoints($user->id);

    if (!$this->userService->checkIfCanAccessToRessource($user->id)) {
      $user->userTrophy = [];
      $user->userPostParticipation = [];
    } else {
      $user->userTrophy;
      $user->userPostParticipation;
      $user->follower;
      $user->following;
    }

    return response()->json($user);
  }

  public function update(Request $request)
  {
    $user = $this->userService->getUser();

    $validated = $request->validate([
      'email' => 'nullable|string|email',
      'username' => 'nullable|string|max:255',
      'image' => 'nullable|string|max:255',
      'badge_id' => 'nullable|integer',
      'birthdate' => 'nullable|date',
      'biography' => 'nullable|string',
      'position' => 'nullable|string|max:255',
      "is_private" => 'nullable|boolean'
    ]);

    $user->update($validated);

    return response()->json($user);
  }

  public function destroy()
  {
    $user = $this->userService->getUser();

    $user->delete();
  }

}