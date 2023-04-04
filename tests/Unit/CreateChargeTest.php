<?php

namespace Dbaeka\StripePayment\Tests\Unit;

use Dbaeka\StripePayment\DataObjects\Charge;
use Dbaeka\StripePayment\Services\Client;
use Dbaeka\StripePayment\Services\CreateCharge;
use Dbaeka\StripePayment\Tests\TestCase;
use Stripe\Exception\ApiConnectionException;
use Stripe\Service\ChargeService;

class CreateChargeTest extends TestCase
{
    public function testCreateChargeSuccessfully(): void
    {
        $charge = new \Stripe\Charge();
        $charge->amount = 100;
        $mock = $this->mock(ChargeService::class);
        $mock->shouldReceive('create')
            ->andReturn($charge)
            ->once();

        $this->mock(Client::class)
            ->shouldReceive('getChargeService')
            ->andReturn($mock);

        $service = app(CreateCharge::class);
        $data = Charge::from([
            'amount' => fake()->randomFloat(2, 2),
            'currency' => strtolower(fake()->currencyCode()),
            'source' => fake()->lexify('???????????'),
            'description' => fake()->sentence(),
            'idempotency_key' => fake()->uuid()
        ]);
        $charge_array = $service->execute($data);
        $this->assertNotEmpty($charge_array);
        $this->assertIsArray($charge_array);
        $this->assertSame(100, $charge_array['amount']);
    }

    public function testCreateChargeFailFromInvalidResponse(): void
    {
        $mock = $this->mock(ChargeService::class);
        $mock->shouldReceive('create')
            ->andReturn([])
            ->once();

        $this->mock(Client::class)
            ->shouldReceive('getChargeService')
            ->andReturn($mock);

        $service = app(CreateCharge::class);
        $data = Charge::from([
            'amount' => fake()->randomFloat(2, 2),
            'currency' => strtolower(fake()->currencyCode()),
            'source' => fake()->lexify('???????????'),
            'description' => fake()->sentence(),
            'idempotency_key' => fake()->uuid()
        ]);
        $charge_array = $service->execute($data);
        $this->assertEmpty($charge_array);
    }

    public function testCreateChargeFailFromException(): void
    {
        $mock = $this->mock(ChargeService::class);
        $mock->shouldReceive('create')
            ->andThrow(ApiConnectionException::class)
            ->never();

        $this->mock(Client::class)
            ->shouldReceive('getChargeService')
            ->andReturn($mock);

        $service = app(CreateCharge::class);
        $data = Charge::from();
        $charge_array = $service->execute($data);
        $this->assertEmpty($charge_array);
    }
}
