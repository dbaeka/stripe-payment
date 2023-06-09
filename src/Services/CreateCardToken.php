<?php

namespace Dbaeka\StripePayment\Services;

use Throwable;
use Stripe\Service\TokenService;
use Illuminate\Support\Facades\Log;
use Dbaeka\StripePayment\DataObjects\CreditCardDetails;

class CreateCardToken
{
    public function __construct(
        private readonly TokenService $client
    ) {
    }

    public function execute(CreditCardDetails $data): ?string
    {
        try {
            $response = $this->client->create([
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
