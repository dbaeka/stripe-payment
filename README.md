# Stripe Payment Service 

This package allows payment to Stripe

## Usage
The package is configured to be used within the main Pets Store.
Check out the [Pets Store Repository](https://github.com/dbaeka/buckhill-pet-commerce) for steps on adding this package as a submodule

* You need to implement the `StripeUpdatable` contract from the `Dbaeke/StripePayment/Contracts/StripeUpdatable`.
This enables the package persist your payment table on success, failure or with the init token values. Typically, implement 
this on your repository class.

* Set the `STRIPE_API_SECRET_KEY` value in the `.env` file to set the API secret key

* You need to publish the config and migrations using
```bash
php artisan vendor:publish --provider="Dbaeka\StripePayment\StripePaymentServiceProvider"
```

* In the `stripe_payment.php` config file, set the following
1. stripe_updatable to your class that implements that implements the StripeUpdatable contract
2. payment_middlewares to add middlewares from your application to the Payment Controller. For eg. `'secure'` to protect the route

*Note*: Callbacks are saved in the `stripe_payments` table after migration. You can overwrite the 
default webhook url in the config file to intercept the payload from stripe

## Testing
To run tests, simply run
```bash
composer test
```

## Linting
To run the lint test, simply run
```bash
composer pint
```

## PHPStan
To run PHP stan, simply run
```bash
composer analyse
```

## PHPInsight
To run PHP Insight, simply run
```bash
composer insight
```