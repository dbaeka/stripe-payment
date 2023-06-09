<?php

namespace Dbaeka\StripePayment\Services;

use Throwable;
use Stripe\Service\TokenService;
use Illuminate\Support\Facades\Log;
use Dbaeka\StripePayment\DataObjects\BankDetails;

class CreateBankToken
{
    public function __construct(
        private readonly TokenService $client
    ) {
    }

    public function execute(BankDetails $data): ?string
    {
        try {
            $response = $this->client->create([
                'bank_account' => [
                    'account_holder_type' => 'individual',
                    'account_holder_name' => $data->holder_name,
                    'account_number' => $data->iban,
                    'currency' => $data->currency,
                    'country' => $data->country,

                ],
            ]);
            return data_get($response, 'id');
        } catch (Throwable $e) {
            Log::error('Stripe Error encountered:  ' . $e->getMessage());
            return null;
        }
    }
}
