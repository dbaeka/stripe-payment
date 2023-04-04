<?php

namespace Dbaeka\StripePayment\Services;

use RuntimeException;
use Stripe\Service\ChargeService;
use Stripe\Service\TokenService;
use Stripe\StripeClient;

class Client
{
    private StripeClient $stripe;

    public function __construct(
        protected string $secret_key,
    ) {
        $this->verifyConfig();
        $this->stripe = new StripeClient(
            $this->secret_key
        );
    }

    public function getTokenService(): TokenService
    {
        return $this->stripe->tokens;
    }

    public function getChargeService(): ChargeService
    {
        return $this->stripe->charges;
    }

    private function verifyConfig(): void
    {
        if (empty($this->secret_key)) {
            throw new RuntimeException('Invalid Config');
        }
    }
}
