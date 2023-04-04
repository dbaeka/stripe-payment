<?php

namespace Dbaeka\StripePayment;

use Dbaeka\StripePayment\Contracts\StripeUpdatable;
use Dbaeka\StripePayment\DataObjects\Charge;
use Dbaeka\StripePayment\DataObjects\CreditCardDetails;
use Dbaeka\StripePayment\DataObjects\Payment;
use Dbaeka\StripePayment\DataObjects\StripeMetadata;
use Dbaeka\StripePayment\Services\CreateBankToken;
use Dbaeka\StripePayment\Services\CreateCardToken;
use Dbaeka\StripePayment\Services\CreateCharge;
use Spatie\LaravelData\Data;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class StripePayment
{
    public const STRIPE_GATEWAY = 'stripe';

    public function __construct(
        private readonly StripeUpdatable $payment_repo
    )
    {
    }

    public function createPayment(string $payment_uuid, Data $details): ?Data
    {
        $stripe_token = $this->handleTokenCreate($details);
        throw_if(empty($stripe_token), new UnprocessableEntityHttpException());
        // Save Payment in Database
        $payment = Payment::from([
            'gateway' => self::STRIPE_GATEWAY,
            'gateway_metadata' => StripeMetadata::from(['token_id' => $stripe_token])
        ]);
        return $this->payment_repo->updatePayment($payment_uuid, $payment);
    }

    private function handleTokenCreate(Data $details): ?string
    {
        $service = $details instanceof CreditCardDetails ? app(CreateCardToken::class) :
            app(CreateBankToken::class);
        return $service->execute($details);
    }

    private function updatePayment(string $status, string $payment_uuid, Payment $payment): ?Data
    {
        if ($status === 'succeeded') {
            return $this->payment_repo->updateSuccess($payment_uuid, $payment);
        }
        if ($status === 'failed') {
            return $this->payment_repo->updateFailure($payment_uuid, $payment);
        }
        return null;
    }

    public function createCharge(Charge $details): ?Data
    {
        $charge_response = app(CreateCharge::class)->execute($details);
        throw_if(empty($charge_response), new UnprocessableEntityHttpException());
        return $this->handleChargeResponse($charge_response, $details);
    }

    /**
     * @param array<string, mixed> $charge_response
     */
    private function handleChargeResponse(array $charge_response, Charge $details): ?Data
    {
        $charge = Charge::from($charge_response);
        /** @var string $payment_uuid */
        $payment_uuid = $details->payment_uuid;
        /** @var string $order_uuid */
        $order_uuid = $details->order_uuid;
        /** @var string $status */
        $status = $charge->status;
        $payment = new Payment();
        $payment->gateway = self::STRIPE_GATEWAY;
        $payment->gateway_metadata = StripeMetadata::from(['charge' => $charge]);
        $redirect_url = $this->getRedirectUrl($order_uuid, $status);
        $payment_data = $this->updatePayment($status, $payment_uuid, $payment);
        return $payment_data?->additional(['redirect_url' => $redirect_url]);
    }

    private function getRedirectUrl(string $order_uuid, string $status): ?string
    {
        $redirect_url = null;
        if ($status === 'succeeded') {
            $redirect_url = url()->to('/') . "/payment/{$order_uuid}/?status=success&gtw=stripe";
        }
        if ($status === 'failed') {
            $redirect_url = url()->to('/') . "/payment/{$order_uuid}/?status=failure&gtw=stripe";
        }
        return $redirect_url;
    }
}
