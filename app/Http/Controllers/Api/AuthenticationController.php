<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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

  public function changePassword(Request $request)
  {
    $user = $this->userService->getUser();
    $input = $request->all();

    $rules = array(
      'old_password' => 'required',
      'new_password' => [
        'password' =>
          'required',
        'min:8',
        'regex:/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).*$/'
      ],
      'confirm_password' => 'required|same:new_password',
    );
    $validator = Validator::make($input, $rules);
    if ($validator->fails()) {
      $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
    } else {
      try {
        if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
          $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
        } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
          $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
        } else {
          User::where('id', $user->id)->update(['password' => Hash::make($input['new_password'])]);
          $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
        }
      } catch (Exception $ex) {
        $msg = $ex->getMessage();
        $arr = array("status" => 400, "message" => $msg, "data" => array());
      }
    }
    return response()->json($arr);
  }
  public function requestResetPassword(Request $request)
  {
    $validatedData = $request->validate([
      'email' => 'required|email'
    ]);

    $user = User::where('email', $validatedData['email'])->firstOrFail();
    $token = Str::random(64);

    DB::table('password_reset_tokens')->insert([
      'email' => $request->email,
      'token' => $token,
      'created_at' => now()
    ]);

    $data = [
      'email' => $validatedData['email'],
      'token' => $token,
    ];

    Mail::to($data['email'])->send(new \App\Mail\ResetPasswordMail($data));

    return response()->json(['message' => 'Email sent to reset password']);
  }

  public function resetPassword(Request $request)
  {
    $input = $request->all();

    $rules = array(
      'token' => 'required',
      'email' => 'required|email',
      'password' => [
        'password' =>
          'required',
        'min:8',
        'regex:/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).*$/'
      ],
      'confirm_password' => 'required|same:password',
    );

    DB::table('password_reset_tokens')->where(['email' => $request->email, 'token' => $request->token])->first();
    $user = User::where('email', $request->email)->firstOrFail();
    $validator = Validator::make($input, $rules);
    if ($validator->fails()) {
      $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
    } else {
      try {
          User::where('id', $user->id)->update(['password' => Hash::make($input['password'])]);
          $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
        
      } catch (Exception $ex) {
        $msg = $ex->getMessage();
        $arr = array("status" => 400, "message" => $msg, "data" => array());
      }
    }
    return response()->json($arr);
  }

}