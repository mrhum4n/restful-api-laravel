<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // get value token dengan key Authorization
        $token = $request->header('Authorization');

        $isAuthenticate = true;

        if (!$token) {
            $isAuthenticate = false;
        }

        // mengambil user berdasarkan token
        $user = User::where('token', $token)->first();
        if (!$user) {
            $isAuthenticate = false;
        }else {
            // lakukan proses login ketika data user ditemukan
            Auth::login($user);
        }

        if ($isAuthenticate) {
            // jika ter-autentikasi, lanjutkan ke controller
            return $next($request);
        }else {
            // jika tidak ter-autentikasi, return response json unauthorized
            return response()->json([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
