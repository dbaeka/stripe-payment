<?php

namespace Dbaeka\StripePayment;

use Dbaeka\StripePayment\Contracts\StripeUpdatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Stripe\Service\ChargeService;
use Stripe\Service\TokenService;
use Stripe\Service\WebhookEndpointService;
use Stripe\StripeClient;

class StripePaymentServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/stripe_payment.php', 'stripe_payment');
        }

        $this->app->register('Spatie\LaravelData\LaravelDataServiceProvider');

        $this->bindStripeServices();

        if (!is_null(config('stripe_payment.stripe_updatable'))) {
            $this->app->bind(StripeUpdatable::class, config('stripe_payment.stripe_updatable'));
        }
    }

    private function bindStripeServices(): void
    {
        $this->app->bind(StripeClient::class, function () {
            $secret_key = config('stripe_payment.secret_key');
            throw_if(empty($secret_key));
            return new StripeClient($secret_key);
        });

        $this->app->bind(TokenService::class, function (Application $app) {
            $client = $app->make(StripeClient::class);
            return new TokenService($client);
        });

        $this->app->bind(ChargeService::class, function (Application $app) {
            $client = $app->make(StripeClient::class);
            return new ChargeService($client);
        });

        $this->app->bind(WebhookEndpointService::class, function (Application $app) {
            $client = $app->make(StripeClient::class);
            return new WebhookEndpointService($client);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/stripe_payment.php' => config_path('stripe_payment.php'),
            ], 'config');
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }
}
