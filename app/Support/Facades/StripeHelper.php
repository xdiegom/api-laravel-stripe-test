<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Stripe\PaymentMethod createPaymentMethod(array $attributes)
 * @method static \Illuminate\Support\Collection|\Laravel\Cashier\PaymentMethod[] getPaymentMethods()
 * @method static \Illuminate\Support\Collection<mixed, object> castPaymentMethods(\Illuminate\Support\Collection);
 * @method static void deletePaymentMethod(string $paymentMethodId);
 *
 * @see \App\Helpers\StripeHelper
 */
class StripeHelper extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'stripe-helper';
    }
}
