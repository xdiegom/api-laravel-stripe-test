<?php

namespace App\Http\Controllers;

use App\Payments\PaymentMethod;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{

    public function pay(Request $request)
    {
        $this->validate($request, [
            'card_number' => ['required'],
            'expiry_month' => ['required'],
            'expiry_year' => ['required'],
            'cvc' => ['required'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', 'string']
        ]);

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
