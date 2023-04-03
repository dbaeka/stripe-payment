<?php

namespace Dbaeka\StripePayment\Tests;

use Dbaeka\StripePayment\StripePaymentServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        config([
            'stripe_payment.secret_key' => 'test_key',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            StripePaymentServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
