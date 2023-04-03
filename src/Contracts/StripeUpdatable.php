<?php

namespace Dbaeka\StripePayment\Contracts;

use Dbaeka\StripePayment\DataObjects\Payment;
use Spatie\LaravelData\Data;

interface StripeUpdatable
{
    public function updatePayment(string $payment_uuid, Payment $data): ?Data;
}
