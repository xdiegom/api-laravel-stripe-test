<?php

namespace App\Payments;

use Stripe\StripeClient;
use Stripe\Token;

class StripePaymentMethod implements PayableInterface
{
    protected StripeClient $client;

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

        $this->client = new StripeClient(config('services.stripe.secret'));
    }

    public function pay(int $amount, string $currency = 'USD'): bool|string
    {
        $token = $this->createToken();

        $charge = $this->client->charges->create([
            'amount' => $amount * 100,
            'currency' => $currency,
            'source' => $token->id
        ]);

        if ($charge->status === 'succeeded' || $charge->status === 'pending') {
            return $charge->id;
        }

        return false;
    }

    protected function createToken(): Token
    {
        return $this->client->tokens->create([
            'card' => [
                'number' => $this->cardNumber,
                'exp_month' => $this->expiryMonth,
                'exp_year' => $this->expiryYear,
                'cvc' => $this->cvc,
            ],
        ]);
    }
}
