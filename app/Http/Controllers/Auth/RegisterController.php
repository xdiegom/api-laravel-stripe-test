<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Support\Facades\StripeHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;
        StripeHelper::createCustomer();

        return response()->json([
            'message' => 'User successfuly created',
            'data' => [
                'token' => $token,
                'type' => 'Bearer'
            ]
        ]);
    }
}
