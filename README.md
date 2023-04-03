# Stripe Payment Service 

This package allows payment to Stripe

## Usage
The package is configured to be used within the main Pets Store.
Check out the [Pets Store Repository](https://github.com/dbaeka/buckhill-pet-commerce) for steps on adding this package as a submodule

Set the `STRIPE_API_SECRET_KEY` value in the `.env` file to set the API secret key

You need to publish the config using
```bash
php artisan vendor:publish --provider="Dbaeka\StripePayment\StripePaymentServiceProvider" --tag="config"
```

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