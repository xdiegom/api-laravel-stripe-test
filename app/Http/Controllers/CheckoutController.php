<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Payments\PaymentMethod;

class CheckoutController extends Controller
{

    public function pay(PaymentRequest $request)
    {
        try {
            $paymentMethod = new PaymentMethod(
                $request->card_number,
                $request->expiry_month,
                $request->expiry_year,
                $request->cvc
            );

            $paymentService = $paymentMethod->initializePaymentMethod($request->type);

            $transactionId = $paymentService->pay($request->amount);

            return response()->json([
                'message' => 'Payment applied successfuly',
                'transaction_id' => $transactionId,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'USD'
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'message' => $th->getMessage()
                ]
            );
        }
    }
}
