<?php

namespace Dbaeka\StripePayment\Tests\Unit;

use Dbaeka\StripePayment\DataObjects\BankDetails;
use Dbaeka\StripePayment\Services\Client;
use Dbaeka\StripePayment\Services\CreateBankToken;
use Dbaeka\StripePayment\Tests\TestCase;
use Stripe\Exception\ApiConnectionException;
use Stripe\Service\TokenService;

class CreateBankTokenTest extends TestCase
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

        $this->mock(Client::class)
            ->shouldReceive('getTokenService')
            ->andReturn($mock);

        $service = app(CreateBankToken::class);
        $data = BankDetails::from([
            'country' => fake()->countryCode(),
            'holder_name' => fake()->name(),
            'iban' => fake()->iban(),
            'currency' => strtolower(fake()->currencyCode())
        ]);
        $token_id = $service->execute($data);
        $this->assertNotEmpty($token_id);
        $this->assertSame("tok_1Mshst2eZvKYlo2CKyQcTdTG", $token_id);
    }

    public function testCreateTokenFailFromInvalidResponse(): void
    {
        $mock = $this->mock(TokenService::class);
        $mock->shouldReceive('create')
            ->andReturn([])
            ->once();

        $this->mock(Client::class)
            ->shouldReceive('getTokenService')
            ->andReturn($mock);

        $service = app(CreateBankToken::class);
        $data = BankDetails::from([
            'country' => fake()->countryCode(),
            'holder_name' => fake()->name(),
            'iban' => fake()->iban(),
            'currency' => strtolower(fake()->currencyCode())
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

        $this->mock(Client::class)
            ->shouldReceive('getTokenService')
            ->andReturn($mock);

        $service = app(CreateBankToken::class);
        $data = BankDetails::from();
        $token_id = $service->execute($data);
        $this->assertEmpty($token_id);
    }
}
