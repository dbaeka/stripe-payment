<?php

namespace Dbaeka\StripePayment\DataObjects;

use Spatie\LaravelData\Data;

class StripeMetadata extends Data
{
    public function __construct(
        public string $token_id,
    ) {
    }
}
