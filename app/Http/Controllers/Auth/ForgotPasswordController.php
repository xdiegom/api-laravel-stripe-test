<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetForgotPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Sends a password reset token if exists
     * @param ForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ForgotPasswordRequest $request)
    {
        $user = User::whereEmail($request->email)->first();

        $userResetPassword = Password::getUser([
            'email' => $request->only('email')
        ]);

        if ($userResetPassword) {
            Password::deleteToken($userResetPassword);
        }

        $user->sendPasswordResetNotification(Password::createToken($user));

        return response()->json([
            'message' => 'A password reset token has been sent to your email'
        ]);
    }

    /**
     * Updates the user password if the sent token matches
     * the one that is stored in the database
     * @param ResetForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ResetForgotPasswordRequest $request)
    {
        $user = User::whereEmail($request->email)->first();

        if (!Password::tokenExists($user, $request->token)) {
            return response()->json([
                'message' => 'Invalid token'
            ], 400);
        }

        Password::deleteToken($user);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password successfully resetted'
        ]);
    }
}
