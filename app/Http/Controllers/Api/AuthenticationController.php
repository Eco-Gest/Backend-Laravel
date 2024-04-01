<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Services\UserService;

class AuthenticationController extends Controller
{

  protected UserService $userService;
  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }

  public function login(Request $request)
  {
    if (!Auth::attempt($request->only('email', 'password'))) {
      return response()->json([
        'message' => 'Login information is invalid.'
      ], 400);
    }

    $user = User::where('email', $request['email'])->firstOrFail();
    $token = $user->createToken('authToken')->plainTextToken;

    return response()->json([
      'access_token' => $token,
      'token_type' => 'Bearer',
    ]);
  }

  /**
   * Summary of register
   * @param \Illuminate\Http\Request $request
   * @return mixed
   */
  public function register(Request $request)
  {
      $validatorEmail = Validator::make($request->all(), [
          'email' => 'required|email',
      ]);

      if ($validatorEmail->fails()) {
          return response()->json([
              'message' => 'Email format is invalid.'
          ], 400);
      }

      if (User::where('email', $request['email'])->count() > 0) {
          return response()->json([
              'message' => 'Email already used.'
          ], 409);
      }

      $validatorUsername = Validator::make($request->all(), [
          'username' => 'required|string|min:5|max:29'
      ]);

      if ($validatorUsername->fails()) {
          return response()->json([
              'message' => 'Username format is invalid (it musts contain between 5 & 29 characters).'
          ], 400);
      }

      if (User::where('username', $request['username'])->count() > 0) {
          return response()->json([
              'message' => 'Username already used.'
          ], 409);
      }

      $validatorPassword = Validator::make($request->all(), [
          'password' => [
              'required',
              'min:8',
              'regex:/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).*$/'
          ]
      ]);

      if ($validatorPassword->fails()) {
          return response()->json([
              'message' => 'Password format is invalid.'
          ], 400);
      }

      $user = User::create([
          'email' => $request['email'],
          'username' => $request['username'],
          'password' => Hash::make($request['password']),
      ]);
      $token = $user->createToken('authToken')->plainTextToken;

      return response()->json([
          'access_token' => $token,
          'token_type' => 'Bearer',
      ]);
  }

  public function resetPassword(Request $request)
  {
    if (!Auth::attempt($request->only('email', 'password'))) {
      return response()->json([
        'message' => 'Login information is invalid.'
      ], 400);
    }


    $user = $this->userService->getUser();

    $validatorPassword = Validator::make($request->all(), [
      'password' => [
        'required',
        'min:8',
        'regex:/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).*$/'
      ]
    ]);

    if ($validatorPassword->fails()) {
      return response()->json([
        'message' => 'Password format is invalid.'
      ], 400);
    }


    $user->update(['password' => Hash::make($request['new_password'])]);    

    $token = $user->createToken('authToken')->plainTextToken;

    return response()->json([
      'access_token' => $token,
      'token_type' => 'Bearer',
    ]);
  }

}