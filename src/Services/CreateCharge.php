<?php

namespace Dbaeka\StripePayment\Services;

use Dbaeka\StripePayment\DataObjects\Charge;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Stripe\Service\ChargeService;
use Stripe\Service\WebhookEndpointService;
use Throwable;

class CreateCharge
{
    public function __construct(
        private readonly ChargeService          $charge_client,
        private readonly WebhookEndpointService $webhook_client
    ) {
        $webhook_url = config('stripe_payment.webhook_url');
        if (empty($webhook_url)) {
            $webhook_url = route('stripe.webhook');
        }
        $this->registerWebhook($webhook_url);
    }

    private function registerWebhook(string $url): void
    {
        try {
            $this->webhook_client->create([
                'url' => $url,
                'enabled_events' => [
                    'charge.failed',
                    'charge.succeeded',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Could not register webhooks. Error: ' . $e->getMessage());
            throw new RuntimeException('failed to register webhooks');
        }
    }

    /**
     * @return array<string,mixed>|null
     */
    public function execute(Charge $data): ?array
    {
        try {
            $response = $this->charge_client->create([
                'amount' => $data->amount * 100,
                'currency' => $data->currency,
                'source' => $data->token,
                'description' => $data->description,
                'metadata' => [
                    'payment_uuid' => $data->payment_uuid ?? '',
                    'order_uuid' => $data->order_uuid ?? '',
                ],
            ], [
                'idempotency_key' => $data->idempotency_key,
            ]);
            return $response->toArray();
        } catch (Throwable $e) {
            Log::error('Stripe Error encountered:  ' . $e->getMessage());
            return null;
        }
    }
}
