<?php

namespace Dbaeka\StripePayment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="InitPaymentRequest",
 *    required={"payment_uuid", "order_uuid", "token", "description", "idempotency_key", "amount", "currency"},
 *    @OA\Property(
 *     property="payment_uuid",
 *     type="string",
 *     description="Payment UUID",
 *    ),
 *    @OA\Property(
 *     property="order_uuid",
 *     type="string",
 *     description="Order UUID",
 *    ),
 *    @OA\Property(
 *     property="token",
 *     type="string",
 *     description="Stripe Generated Token",
 *    ),
 *    @OA\Property(
 *     property="description",
 *     type="string",
 *     description="Charge description",
 *    ),
 *    @OA\Property(
 *     property="amount",
 *     type="number",
 *     description="Order amount",
 *    ),
 *    @OA\Property(
 *     property="idempotency_key",
 *     type="string",
 *     description="Key for Stripe Idempotency",
 *    ),
 *    @OA\Property(
 *     property="currency",
 *     type="string",
 *     description="Currency ISO 3 digit",
 *     pattern="^\[a-z]{3}$"
 *    ),
 * )
 */
class CompletePaymentRequest extends FormRequest
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
        $supported_currencies = config('stripe_payment.supported_currencies');
        $supported_currencies = strtolower(implode(',', $supported_currencies));
        return [
            'payment_uuid' => ['required', 'string'],
            'order_uuid' => ['required', 'string'],
            'idempotency_key' => ['required', 'string'],
            'token' => ['required', 'string'],
            'description' => ['required', 'string', 'max:256'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'currency' => ['required', 'string', 'size:3', 'lowercase', 'in:' . $supported_currencies],
        ];
    }
}
