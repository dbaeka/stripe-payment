<?php

namespace Dbaeka\StripePayment;

use Spatie\LaravelData\Data;
use Dbaeka\StripePayment\DataObjects\Charge;
use Dbaeka\StripePayment\DataObjects\Payment;
use Dbaeka\StripePayment\Services\CreateCharge;
use Dbaeka\StripePayment\DataObjects\BankDetails;
use Dbaeka\StripePayment\Services\CreateBankToken;
use Dbaeka\StripePayment\Services\CreateCardToken;
use Dbaeka\StripePayment\Contracts\StripeUpdatable;
use Dbaeka\StripePayment\DataObjects\StripeMetadata;
use Dbaeka\StripePayment\DataObjects\CreditCardDetails;

class StripePayment
{
    public const STRIPE_GATEWAY = 'stripe';

    public function __construct(
        private readonly StripeUpdatable $payment_repo
    ) {
    }

    public function createPayment(string $payment_uuid, Data $details): ?Data
    {
        $stripe_token = $this->handleTokenCreate($details);
        if ($stripe_token) {
            // Save Payment in Database
            $payment = new Payment();
            $payment->gateway = self::STRIPE_GATEWAY;
            $payment->gateway_metadata = StripeMetadata::from(['token_id' => $stripe_token]);
            return $this->payment_repo->updatePayment($payment_uuid, $payment);
        }
        return null;
    }

    private function handleTokenCreate(Data $details): ?string
    {
        $stripe_token = null;
        if ($details instanceof CreditCardDetails) {
            $stripe_token = app(CreateCardToken::class)->execute($details);
        } elseif ($details instanceof BankDetails) {
            $stripe_token = app(CreateBankToken::class)->execute($details);
        }
        return $stripe_token;
    }

    public function createCharge(Charge $details): ?Data
    {
        $charge_response = app(CreateCharge::class)->execute($details);
        if ($charge_response) {
            return $this->handleChargeResponse($charge_response, $details);
        }
        return null;
    }

    /**
     * @param array<string, mixed> $charge_response
     */
    private function handleChargeResponse(array $charge_response, Charge $details): ?Data
    {
        $charge = Charge::from($charge_response);
        /** @var string $payment_uuid */
        $payment_uuid = $details->payment_uuid;
        $payment = new Payment();
        $payment->gateway = self::STRIPE_GATEWAY;
        $payment->gateway_metadata = StripeMetadata::from(['charge' => $charge]);
        if ($charge->status === 'succeeded') {
            return $this->payment_repo->updateSuccess($payment_uuid, $payment);
        }
        if ($charge->status === 'failed') {
            return $this->payment_repo->updateFailure($payment_uuid, $payment);
        }
        return null;
    }
}
