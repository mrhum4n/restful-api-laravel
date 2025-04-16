<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse {
        $data = $request->validated();

        // validasi ketika user yang diregister sama
        if (User::where('username', $data['username'])->count() == 1) {
            throw new HttpResponseException(response([
                'errors' => [
                    'username' => [
                        'username already registered'
                    ]
                ]
            ], 400));
        }

        // save user data
        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): UserResource {
        $data = $request->validated();

        // get data user by username
        $user = User::where('username', $data['username'])->first();

        // validasi user dan password
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'username or password wrong!'
                    ]
                ]
            ], 401));
        }

        // jika user ada dan password benar, lakukan insert token di tbl user
        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);
    }

    public function getUser(Request $request): UserResource {
        return new UserResource(Auth::user());
    }
}
