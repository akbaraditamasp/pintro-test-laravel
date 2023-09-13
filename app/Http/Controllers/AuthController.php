<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        [
            'username' => $username,
            'password' => $password,
        ] = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        /**
         * @var $user User
         */
        $user = User::query()->where("users.username", $username)->with("profile")->firstOrFail();

        if (!(Hash::check($password, $user->password))) {
            return response([
                'message' => "Unauthenticated"
            ], 401);
        }

        $token = $user->createToken("api");

        return $user->toArray() + [
                "token" => $token->plainTextToken
            ];
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();

        return $user->toArray();
    }

    public function checkToken(Request $request)
    {
        return $request->user()->toArray();
    }
}
