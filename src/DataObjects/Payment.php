<?php

namespace Dbaeka\StripePayment\DataObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class Payment extends Data
{
    public string|Optional $gateway;
    public StripeMetadata|Optional $gateway_metadata;
    public string|Optional|null $redirect_url;
}
