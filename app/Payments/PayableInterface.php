<?php

namespace App\Payments;

interface PayableInterface
{
    public function pay(int $amount, string $currency = 'USD'): bool|string;
}
