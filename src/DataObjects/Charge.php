<?php

namespace Dbaeka\StripePayment\DataObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class Charge extends Data
{
    public float $amount;
    public string $currency;
    public string|Optional $token;
    public string|Optional $idempotency_key;
    public string|Optional $description;
    public string|Optional $payment_uuid;
    public string|Optional $order_uuid;
    public string|Optional $status;
    public string|Optional $id;
    public bool|Optional $paid;
    public string|Optional $receipt_url;
}
