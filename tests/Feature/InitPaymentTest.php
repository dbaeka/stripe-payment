<?php

namespace Dbaeka\StripePayment\Tests\Feature;

use Dbaeka\StripePayment\Contracts\StripeUpdatable;
use Dbaeka\StripePayment\DataObjects\Payment;
use Dbaeka\StripePayment\Services\CreateBankToken;
use Dbaeka\StripePayment\Services\CreateCardToken;
use Dbaeka\StripePayment\Tests\TestCase;

class InitPaymentTest extends TestCase
{
    public const PREFIX = 'api/v1/stripe/';

    public function testCreateCreditCardPayment(): void
    {
        $endpoint = self::PREFIX . 'init-payment';
        $data = [
            'payment_uuid' => fake()->uuid(),
            'type' => 'credit_card',
            'details' => [
                'cvv' => 100,
                'holder_name' => fake()->name(),
                'number' => fake()->creditCardNumber(),
                'expiry_date' => fake()->creditCardExpirationDateString()
            ]
        ];
        $this->setupMocks($data['payment_uuid']);
        $this->assertResponse($endpoint, $data);
    }

    private function setupMocks(string $payment_uuid): void
    {
        $this->mock(StripeUpdatable::class)
            ->shouldReceive('updatePayment')
            ->andReturn(Payment::from([
                'gateway' => 'stripe',
                'gateway_metadata' => ['token_id' => 'test_token']
            ])->additional(['uuid' => $payment_uuid]));

        $this->mock(CreateCardToken::class)
            ->shouldReceive('execute')
            ->andReturn('test_token');

        $this->mock(CreateBankToken::class)
            ->shouldReceive('execute')
            ->andReturn('test_token');
    }

    /**
     * @param array<string,mixed> $data
     */
    private function assertResponse(string $endpoint, array $data): void
    {
        $response = $this->postJson($endpoint, $data);
        $response->assertOk()
            ->assertJsonStructure([
                'success', 'error', 'errors',
                'data' => ['uuid', 'gateway', 'gateway_metadata' => ['token_id']],
            ])
            ->assertJsonFragment([
                'success' => 1,
                'token_id' => 'test_token'
            ]);
        $this->postJson($endpoint, [
            'type' => 'regular@test.com',
            'details' => 'secret',
        ])->assertUnprocessable();
    }

    public function testCreateBankCardPayment(): void
    {
        $endpoint = self::PREFIX . 'init-payment';
        $data = [
            'payment_uuid' => fake()->uuid(),
            'type' => 'bank_transfer',
            'details' => [
                'country' => fake()->countryCode(),
                'holder_name' => fake()->name(),
                'iban' => fake()->iban(),
                'currency' => strtolower(fake()->currencyCode())
            ]
        ];
        $this->setupMocks($data['payment_uuid']);
        $this->assertResponse($endpoint, $data);
    }
}
