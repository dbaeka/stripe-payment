<?php

namespace Dbaeka\StripePayment\Tests\Unit;

use Dbaeka\StripePayment\DataObjects\CreditCardDetails;
use Dbaeka\StripePayment\Services\CreateCardToken;
use Dbaeka\StripePayment\Tests\TestCase;
use RuntimeException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Service\TokenService;

class CreateCardTokenTest extends TestCase
{
    public function testCreateTokenSuccessfully(): void
    {
        $mock = $this->mock(TokenService::class);
        $mock->shouldReceive('create')
            ->andReturn([
                "id" => "tok_1Mshst2eZvKYlo2CKyQcTdTG",
                "object" => "token",
                "card" => []
            ])
            ->once();
        $service = app(CreateCardToken::class);
        $data = CreditCardDetails::from([
            'cvv' => 100,
            'holder_name' => fake()->name(),
            'number' => fake()->creditCardNumber(),
            'expiry_date' => fake()->creditCardExpirationDateString()
        ]);
        $token_id = $service->execute($data);
        $this->assertNotEmpty($token_id);
        $this->assertSame("tok_1Mshst2eZvKYlo2CKyQcTdTG", $token_id);
    }

    public function testCreateFailsMissingSecretKey(): void
    {
        config([
            'stripe_payment.secret_key' => '',
        ]);

        $this->expectException(RuntimeException::class);
        app(CreateCardToken::class);
    }

    public function testCreateTokenFailFromInvalidResponse(): void
    {
        $mock = $this->mock(TokenService::class);
        $mock->shouldReceive('create')
            ->andReturn([])
            ->once();
        $service = app(CreateCardToken::class);
        $data = CreditCardDetails::from([
            'cvv' => 100,
            'holder_name' => fake()->name(),
            'number' => fake()->creditCardNumber(),
            'expiry_date' => fake()->creditCardExpirationDateString()
        ]);
        $token_id = $service->execute($data);
        $this->assertEmpty($token_id);
    }


    public function testCreateTokenFailFromException(): void
    {
        $mock = $this->mock(TokenService::class);
        $mock->shouldReceive('create')
            ->andThrow(ApiConnectionException::class)
            ->never();
        $service = app(CreateCardToken::class);
        $data = CreditCardDetails::from();
        $token_id = $service->execute($data);
        $this->assertEmpty($token_id);
    }
}
