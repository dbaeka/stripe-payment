<?php

namespace Dbaeka\StripePayment\Services;

use Dbaeka\StripePayment\DataObjects\CreditCardDetails;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateCardToken
{
    public function __construct(
        private readonly Client $client
    ) {
    }

    public function execute(CreditCardDetails $data): ?string
    {
        try {
            $response = $this->client->getTokenService()->create([
                'card' => [
                    'name' => $data->holder_name,
                    'number' => $data->number,
                    'exp_month' => $data->expiry_date->month,
                    'exp_year' => $data->expiry_date->year,
                    'cvc' => $data->cvv,
                ],
            ]);
            return data_get($response, 'id');
        } catch (Throwable $e) {
            Log::error('Stripe Error encountered:  ' . $e->getMessage());
            return null;
        }
    }
}
