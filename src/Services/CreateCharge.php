<?php

namespace Dbaeka\StripePayment\Services;

use Dbaeka\StripePayment\DataObjects\Charge;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateCharge
{
    public function __construct(
        private readonly Client $client
    ) {
    }

    /**
     * @return array<string,mixed>|null
     */
    public function execute(Charge $data): ?array
    {
        try {
            $response = $this->client->getChargeService()->create([
                'amount' => $data->amount * 100,
                'currency' => $data->currency,
                'source' => $data->token,
                'description' => $data->description,
                'idempotency_key' => $data->idempotency_key,
                'metadata' => [
                    'payment_uuid' => $data->payment_uuid ?? '',
                    'order_uuid' => $data->order_uuid ?? ''
                ],
            ]);
            return $response->toArray();
        } catch (Throwable $e) {
            Log::error('Stripe Error encountered:  ' . $e->getMessage());
            return null;
        }
    }
}
