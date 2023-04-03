<?php

namespace Dbaeka\StripePayment\Tests\Unit;

use Dbaeka\StripePayment\Services\Client;
use Dbaeka\StripePayment\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testCreateClientSuccessfully(): void
    {
        config([
            'stripe_payment.secret_key' => 'test_key',
            'stripe_payment.publishable_key' => 'test_key',
        ]);

        $client = app(Client::class);
        $this->assertNotEmpty($client);
    }

    public function testCreateClientFailsMissingKeys(): void
    {
        config([
            'stripe_payment.secret_key' => '',
            'stripe_payment.publishable_key' => '',
        ]);
        $this->expectException(\RuntimeException::class);
        app(Client::class);
    }

    public function testCreateClientFailsMissingSecretKey(): void
    {
        config([
            'stripe_payment.secret_key' => '',
            'stripe_payment.publishable_key' => 'test_key',
        ]);

        $this->expectException(\RuntimeException::class);
        app(Client::class);
    }
}
