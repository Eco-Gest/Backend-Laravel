<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
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
}