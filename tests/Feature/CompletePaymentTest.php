<?php

namespace Dbaeka\StripePayment\Tests\Feature;

use Dbaeka\StripePayment\Contracts\StripeUpdatable;
use Dbaeka\StripePayment\DataObjects\Payment;
use Dbaeka\StripePayment\Services\CreateCharge;
use Dbaeka\StripePayment\Tests\TestCase;

class CompletePaymentTest extends TestCase
{
    public const PREFIX = 'api/v1/stripe/';

    public function testCreateCreditCardPayment(): void
    {
        $endpoint = self::PREFIX . 'complete-payment';
        $data = [
            'payment_uuid' => fake()->uuid(),
            'order_uuid' => fake()->uuid(),
            'idempotency_key' => fake()->uuid(),
            'token' => fake()->uuid(),
            'currency' => 'eur',
            'description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 2)
        ];
        $this->setupMocks($data['amount'], $data['currency']);
        $this->assertResponse($endpoint, $data);
    }

    private function setupMocks(float $amount, string $currency): void
    {
        $charge = [
            'id' => fake()->uuid(),
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'succeeded'
        ];
        $this->mock(StripeUpdatable::class)
            ->shouldReceive('updateSuccess')
            ->andReturn(Payment::from([
                'gateway' => 'stripe',
                'gateway_metadata' => ['charge' => $charge]
            ]));

        $this->mock(CreateCharge::class)
            ->shouldReceive('execute')
            ->andReturn($charge);
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
                'data' => ['gateway', 'gateway_metadata' => ['charge']],
            ])
            ->assertJsonFragment([
                'success' => 1,
            ]);
        $this->postJson($endpoint, [
            'type' => 'regular@test.com',
            'details' => 'secret',
        ])->assertUnprocessable();
    }
}
