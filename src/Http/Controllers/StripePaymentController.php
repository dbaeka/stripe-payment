<?php

namespace Dbaeka\StripePayment\Http\Controllers;

use Dbaeka\StripePayment\DataObjects\BankDetails;
use Dbaeka\StripePayment\DataObjects\Charge;
use Dbaeka\StripePayment\DataObjects\CreditCardDetails;
use Dbaeka\StripePayment\Enums\PaymentType;
use Dbaeka\StripePayment\Http\Requests\CompletePaymentRequest;
use Dbaeka\StripePayment\Http\Requests\InitPaymentRequest;
use Dbaeka\StripePayment\Http\Resources\BaseResource;
use Dbaeka\StripePayment\StripePayment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @OA\Tag(
 *     name="Stripe Payment",
 *     description="Stripe Payment API endpoint"
 * )
 */
class StripePaymentController extends BaseController
{
    use ValidatesRequests;
    use AuthorizesRequests;

    public function __construct()
    {
        $middlewares = config('stripe_payment.payment_middlewares');
        $this->middleware($middlewares)->only('createPayment');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/stripe/init-payment",
     *     operationId="stripe-payment-init",
     *     summary="Start a new Stripe payment",
     *     tags={"Stripe Payment"},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        ref="#/components/schemas/InitPaymentRequest"
     *       )
     *      )
     *     ),
     *     @OA\Response(response=200, description="Ok"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="NotFound"),
     *     @OA\Response(response=422, description="Unprocessable"),
     *     @OA\Response(response=500, description="ServerError")
     * )
     */
    public function initPayment(
        InitPaymentRequest $request,
        StripePayment      $stripe_payment
    ): BaseResource {
        $data = $request->validated();
        $details = $data['details'];
        if ($data['type'] === PaymentType::CREDIT_CARD->value) {
            $details = CreditCardDetails::from($details);
        } elseif ($data['type'] === PaymentType::BANK_TRANSFER->value) {
            $details = BankDetails::from($details);
        }
        $payment = $stripe_payment->createPayment($data['payment_uuid'], $details);
        return $payment ? new BaseResource($payment) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/stripe/complete-payment",
     *     operationId="stripe-payment-complete",
     *     summary="Complete a Stripe payment with created token",
     *     tags={"Stripe Payment"},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        ref="#/components/schemas/CompletePaymentRequest"
     *       )
     *      )
     *     ),
     *     @OA\Response(response=200, description="Ok"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="NotFound"),
     *     @OA\Response(response=422, description="Unprocessable"),
     *     @OA\Response(response=500, description="ServerError")
     * )
     */
    public function completePayment(
        CompletePaymentRequest $request,
        StripePayment          $stripe_payment
    ): BaseResource {
        $data = $request->validated();
        $details = Charge::from($data);
        $charge = $stripe_payment->createCharge($details);
        return $charge ? new BaseResource($charge) : throw new UnprocessableEntityHttpException();
    }
}
