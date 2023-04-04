<?php

namespace Dbaeka\StripePayment\Contracts;

use Spatie\LaravelData\Data;
use Dbaeka\StripePayment\DataObjects\Payment;

interface StripeUpdatable
{
    public function updatePayment(string $payment_uuid, Payment $data): ?Data;

    public function updateSuccess(string $payment_uuid, Payment $data): ?Data;

    public function updateFailure(string $payment_uuid, Payment $data): ?Data;
}
