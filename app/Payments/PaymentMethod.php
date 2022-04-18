<?php

namespace App\Payments;

class PaymentMethod
{
    public function __construct(
        protected string $cardNumber,
        protected int $expiryMonth,
        protected int $expiryYear,
        protected string $cvc
    ) {
        $this->cardNumber = $cardNumber;
        $this->expiryMonth = $expiryMonth;
        $this->expiryYear = $expiryYear;
        $this->cvc = $cvc;
    }

    public function initializePaymentMethod(string $method)
    {
        if ($method === 'stripe') {
            return new StripePaymentMethod(
                $this->cardNumber,
                $this->expiryMonth,
                $this->expiryYear,
                $this->cvc
            );
        }

        throw new \Exception("Unsupported payment method");
    }
}
