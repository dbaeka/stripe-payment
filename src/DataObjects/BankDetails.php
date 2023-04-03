<?php

namespace Dbaeka\StripePayment\DataObjects;

use Spatie\LaravelData\Data;

/**
 * @OA\Schema(
 *    schema="BankStripeDetails",
 *    type="object",
 *    required={"iban", "holder_name", "country", "currency"},
 *    @OA\Property(
 *     property="iban",
 *     type="string",
 *     description="Bank IBAN",
 *    ),
 *    @OA\Property(
 *     property="holder_name",
 *     type="string",
 *     description="Name on Bank account",
 *    ),
 *    @OA\Property(
 *     property="country",
 *     type="string",
 *     description="Country ISO 3166-2",
 *     pattern="^\[A-Za-z]{2}$"
 *    ),
 *     @OA\Property(
 *     property="currency",
 *     type="string",
 *     description="Currency ISO 3 digit",
 *     pattern="^\[a-z]{3}$"
 *    ),
 * )
 */
class BankDetails extends Data
{
    public string $iban;
    public string $country;
    public string $holder_name;
    public string $currency;
}
