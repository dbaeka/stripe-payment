<?php

namespace Dbaeka\StripePayment\Http\Requests;

use Dbaeka\StripePayment\Enums\PaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * @OA\Schema(
 *    schema="StripePaymentRequest",
 *    required={"payment_uuid", "type", "details"},
 *    @OA\Property(
 *     property="payment_uuid",
 *     type="string",
 *     description="Payment UUID",
 *    ),
 *    @OA\Property(
 *     property="type",
 *     type="string",
 *     description="Payment type",
 *     enum={"credit_card", "bank_transfer"}
 *    ),
 *    @OA\Property(
 *     property="details",
 *     type="object",
 *     description="Payment details",
 *     oneOf={
 *       @OA\Schema(ref="#/components/schemas/CreditCardStripeDetails"),
 *       @OA\Schema(ref="#/components/schemas/BankStripeDetails"),
 *     }
 *    ),
 * )
 */
class StripePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'payment_uuid' => ['required', 'string'],
            'type' => ['required', new Enum(PaymentType::class)],
            'details' => ['required', 'array'],
            'details.number' => ['required_if:type,credit_card', 'numeric'],
            'details.holder_name' => ['required_if:type,credit_card,bank_transfer', 'string'],
            'details.cvv' => ['required_if:type,credit_card', 'numeric'],
            'details.expiry_date' => ['required_if:type,credit_card', 'date_format:m/y'],
            'details.iban' => ['required_if:type,bank_transfer'],
            'details.country' => ['required_if:type,bank_transfer', 'string', 'size:2'],
            'details.currency' => ['required_if:type,bank_transfer', 'string', 'size:3', 'lowercase'],
        ];
    }
}
