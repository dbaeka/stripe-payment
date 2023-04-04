<?php

return [
    //-------------SECRET KEYS----------------
    'secret_key' => env('STRIPE_API_SECRET_KEY', ''),
    'publishable_key' => env('STRIPE_API_PUBLISHABLE_KEY', ''),

    //------------BINDING--------------------
    // Assign Class that Implements the StripeUpdatable contract here for automatic binding
    'stripe_updatable' => null,

    //-----------SUPPORTED CURRENCIES----------
    'supported_currencies' => ['USD', 'EUR'],

    //-----------WEBHOOK URL----------------
    'webhook_url' => null,

    //-----------PAYMENT CONTROLLER MIDDLEWARES-------
    'payment_middlewares' => []
];
