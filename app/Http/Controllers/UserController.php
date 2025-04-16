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
use App\Http\Requests\UserUpdateRequest;
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

    public function update(UserUpdateRequest $request): UserResource {
        $data = $request->validated();
        
        // ambil user yang login saat ini
        $userId = Auth::user()->id;

        // query get user by id
        $user = User::where('id', $userId)->first();

        if (!$user) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'user not found!'
                    ]
                ]
            ], 404));
        }

        // melakukan update data
         if (isset($data['name'])) {
            $user->name = $data['name'];
         }

         if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
         }

         // save user ke db}
         $user->save();

         return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse {
        $userLoginId = Auth::user()->id;

        $user = User::where('id', $userLoginId)->first();
        $user->token = null;
        $user->save();

        return response()->json([
            'data' => [
                'error' => false,
                'message' => 'logout success!'
            ]
        ])->setStatusCode(200);
    }
}
