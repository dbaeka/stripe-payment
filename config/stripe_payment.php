<?php

return [
    //-------------SECRET KEYS----------------
    'secret_key' => env('STRIPE_API_SECRET_KEY', ''),
    'publishable_key' => env('STRIPE_API_PUBLISHABLE_KEY', ''),

    //------------BINDING--------------------
    // Assign Class that Implements the StripeUpdatable contract here for automatic binding
    'stripe_updatable' => null
];
