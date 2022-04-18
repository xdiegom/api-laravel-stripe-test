<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Attempts to logs in a user
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::whereEmail($request->email)->first();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Successfully logged in!',
                'data' => [
                    'token' => $user->createToken('auth_token')->plainTextToken,
                    'type' => 'Bearer'
                ]
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ]);
    }

    /**
     * Logs out the current user and deletes all generated tokens
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
