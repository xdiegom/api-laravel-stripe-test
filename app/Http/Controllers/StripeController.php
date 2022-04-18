<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeController extends Controller
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function pay(Request $request)
    {
        $this->validate($request, [
            'card_number' => ['required'],
            'expiry_month' => ['required'],
            'expiry_year' => ['required'],
            'cvc' => ['required'],
            'amount' => ['required', 'numeric', 'min:0.01']
        ]);

        try {
            $token = $this->stripe->tokens->create([
                'card' => [
                    'number' => $request->card_number,
                    'exp_month' => $request->expiry_month,
                    'exp_year' => $request->expiry_year,
                    'cvc' => $request->cvc,
                ],
            ]);

            $charge = $this->stripe->charges->create([
                'amount' => $request->amount * 100,
                'currency' => 'USD',
                'source' => $token->id
            ]);

            if ($charge->status === 'succeeded') {
                return response()->json([
                    'payment_status' => 'succeeded',
                    'amount' => $request->amount,
                    'currency' => 'USD'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'message' => $th->getMessage()
                ]
            );
        }
    }
}
