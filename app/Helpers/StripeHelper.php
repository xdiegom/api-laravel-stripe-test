<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Collection;

class StripeHelper
{
    public function __construct(protected User $user)
    {
        $this->user = $user;
    }

    /**
     * Creates a customer for the authenticated user
     * @return Customer
     */
    public function createCustomer()
    {
        return $this->user->createOrGetStripeCustomer(
            [
                'name' => $this->user->first_name . ' ' . $this->user->last_name,
                'email' => $this->user->email
            ]
        );
    }

    /**
     * Cast to PaymentMethods to a standard object collection
     * @param Collection<mixed, App\Models\PaymentMethod> $paymentMethods
     * @return Collection<mixed, object>
     */
    public function castPaymentMethods(Collection $paymentMethods)
    {
        $stripePaymentMethods = $this->getPaymentMethods();

        return $paymentMethods->map(function ($paymentMethod) use ($stripePaymentMethods) {
            $stripePaymentMethod = $stripePaymentMethods->first(function ($stripePaymentMethod) use ($paymentMethod) {
                return $paymentMethod->stripe_payment_method_id === $stripePaymentMethod->id;
            });

            return (object)[
                'id' => $paymentMethod->id,
                'alias' => $paymentMethod->alias,
                'brand' => $stripePaymentMethod->card->brand,
                'default' => $paymentMethod->default,
                'last4' => $stripePaymentMethod->card->last4,
                'expiration_year' => $stripePaymentMethod->card->exp_year,
                'expiration_month' => $stripePaymentMethod->card->exp_month
            ];
        });
    }

    /**
     * Deletes the given payment method
     * @param string $paymentMethodId
     * @return void
     */
    public function deletePaymentMethod(string $paymentMethodId)
    {
        $this->user->deletePaymentMethod($paymentMethodId);
    }

    /**
     * Gets the user paymentMethods
     * @return \Illuminate\Support\Collection|\Laravel\Cashier\PaymentMethod[]
     */
    protected function getPaymentMethods()
    {
        return $this->user->paymentMethods();
    }
}
