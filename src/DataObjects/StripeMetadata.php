<?php

namespace Dbaeka\StripePayment\DataObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class StripeMetadata extends Data
{
    public function __construct(
        public string|Optional $token_id,
        public Charge|Optional $charge
    ) {
    }
}
