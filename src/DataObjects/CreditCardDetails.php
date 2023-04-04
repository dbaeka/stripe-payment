<?php

namespace Dbaeka\StripePayment\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

/**
 * @OA\Schema(
 *    schema="CreditCardStripeDetails",
 *    type="object",
 *    required={"number", "cvv", "holder_name", "expiry_date"},
 *    @OA\Property(
 *     property="number",
 *     type="string",
 *     description="Card number",
 *    ),
 *    @OA\Property(
 *     property="cvv",
 *     type="string",
 *     description="Card CVV",
 *    ),
 *    @OA\Property(
 *     property="holder_name",
 *     type="string",
 *     description="Card holder name",
 *    ),
 *    @OA\Property(
 *     property="expiry_date",
 *     type="string",
 *     description="Card expiry (MM/YY)",
 *     pattern="^\d{2}/\d{2}$"
 *    ),
 * )
 */

class CreditCardDetails extends Data
{
    public string $number;
    public string $cvv;
    public string $holder_name;
    #[WithCast(DateTimeInterfaceCast::class, format: 'm/y')]
    public CarbonImmutable $expiry_date;
}
