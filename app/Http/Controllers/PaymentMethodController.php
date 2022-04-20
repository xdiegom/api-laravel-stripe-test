<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Support\Facades\StripeHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    /**
     * The authenticated user
     * @var User
     */
    protected User $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }

    /**
     * Returns all user's payment methods
     * @return JsonResponse
     */
    public function index()
    {
        $paymentMethods = $this->user->stripePaymentMethods;

        $cards = StripeHelper::castPaymentMethods($paymentMethods);

        return response()->json(
            PaymentMethodResource::collection($cards)
        );
    }

    /**
     * Stores a payment method
     * @param CreatePaymentMethodRequest $request
     * @return JsonResponse
     */
    public function store(CreatePaymentMethodRequest $request)
    {
        try {
            $stripePaymentMethod = $this->user->findPaymentMethod($request->payment_method_id);

            if (!$stripePaymentMethod) {
                return response()->json([
                    'message' => 'Payment method is invalid'
                ], 400);
            }

            $isFirstCard = !$this->user->stripePaymentMethods()->count();
            $paymentMethod = $this->user->stripePaymentMethods()->firstOrCreate([
                'stripe_payment_method_id' => $stripePaymentMethod->id,
                'default' => $isFirstCard
            ]);

            if ($isFirstCard) {
                $this->user->updateDefaultPaymentMethod($stripePaymentMethod->id);
            }

            if (!$paymentMethod->wasRecentlyCreated) {
                $card = StripeHelper::castPaymentMethods(
                    collect()->push($paymentMethod)
                )->first();

                return response()->json(
                    new PaymentMethodResource($card)
                );
            }

            $paymentMethod->alias = $request->alias;
            $paymentMethod->save();

            $card = StripeHelper::castPaymentMethods(
                collect()->push($paymentMethod)
            )->first();

            return response()->json(
                new PaymentMethodResource($card)
            );
        } catch (\Throwable $th) {
            Log::error(
                'File: ' . $th->getFile() .
                    ' Line: ' . $th->getLine() .
                    ' Error: ' . $th->getMessage()
            );

            return response()->json([
                'message' =>
                app()->environment('production') ?
                    'Somenthing went wrong. Please try again' : $th->getMessage()
            ], 500);
        }
    }

    /**
     * Updates the given payment method
     * @param UpdatePaymentMethodRequest $request
     * @param PaymentMethod $paymentMethod
     * @return JsonResponse
     */
    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod->alias = $request->alias ??= $paymentMethod->alias;
            $paymentMethod->default = $request->default ??= $paymentMethod->default;

            if ($request->default) {
                $this->user->stripePaymentMethods()->update(['default' => false]);
                $this->user->updateDefaultPaymentMethod($paymentMethod->stripe_payment_method_id);
            }

            $paymentMethod->save();

            $card = StripeHelper::castPaymentMethods(
                collect()->push($paymentMethod)
            )->first();

            return response()->json(
                new PaymentMethodResource($card)
            );
        } catch (\Throwable $th) {
            Log::error(
                'File: ' . $th->getFile() .
                    ' Line: ' . $th->getLine() .
                    ' Error: ' . $th->getMessage()
            );

            return response()->json([
                'message' =>
                app()->environment('production') ?
                    'Somenthing went wrong. Please try again' : $th->getMessage()
            ], 500);
        }
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            StripeHelper::deletePaymentMethod($paymentMethod->stripe_payment_method_id);

            $paymentMethod->delete();

            if (!($this->user->stripePaymentMethods->count() > 1)) {
                $defaultPaymentMethod = $this->user->stripePaymentMethods->first();
                $defaultPaymentMethod->default = true;
                $defaultPaymentMethod->save();

                $this->user->updateDefaultPaymentMethod($defaultPaymentMethod->stripe_payment_method_id);
            }

            return response()->noContent();
        } catch (\Throwable $th) {
            Log::error(
                'File: ' . $th->getFile() .
                    ' Line: ' . $th->getLine() .
                    ' Error: ' . $th->getMessage()
            );

            return response()->json([
                'message' =>
                app()->environment('production') ?
                    'Somenthing went wrong. Please try again' : $th->getMessage()
            ], 500);
        }
    }
}
