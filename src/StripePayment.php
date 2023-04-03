<?php

namespace Dbaeka\StripePayment;

use Dbaeka\StripePayment\Contracts\StripeUpdatable;
use Dbaeka\StripePayment\DataObjects\BankDetails;
use Dbaeka\StripePayment\DataObjects\CreditCardDetails;
use Dbaeka\StripePayment\DataObjects\Payment;
use Dbaeka\StripePayment\DataObjects\StripeMetadata;
use Dbaeka\StripePayment\Services\CreateBankToken;
use Dbaeka\StripePayment\Services\CreateCardToken;
use Spatie\LaravelData\Data;

class StripePayment
{
    public const STRIPE_GATEWAY = 'stripe';

    public function __construct(
        private readonly StripeUpdatable $payment_repo
    ) {
    }

    public function createPayment(string $payment_uuid, Data $details): ?Data
    {
        $stripe_token = null;
        // Create token Stripe
        if ($details instanceof CreditCardDetails) {
            $stripe_token = app(CreateCardToken::class)->execute($details);
        } elseif ($details instanceof BankDetails) {
            $stripe_token = app(CreateBankToken::class)->execute($details);
        }

        if ($stripe_token) {
            // Save Payment in Database
            $payment = new Payment();
            $payment->gateway = self::STRIPE_GATEWAY;
            $payment->metadata = StripeMetadata::from(['token_id' => $stripe_token]);
            return $this->payment_repo->updatePayment($payment_uuid, $payment);
        }

        return null;
    }
}
