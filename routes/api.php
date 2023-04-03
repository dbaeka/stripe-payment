<?php

use Dbaeka\StripePayment\Http\Controllers\StripePaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('api/v1/stripe')->name('stripe.')->group(function (): void {
    Route::post('init-payment', [StripePaymentController::class, 'initPayment'])->name('init-payment');
});
