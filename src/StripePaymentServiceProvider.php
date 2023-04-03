<?php

namespace Dbaeka\StripePayment;

use Dbaeka\StripePayment\Contracts\StripeUpdatable;
use Dbaeka\StripePayment\Services\Client;
use Illuminate\Support\ServiceProvider;

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

        $this->app->singleton(Client::class, function () {
            return new Client(
                secret_key: config('stripe_payment.secret_key'),
            );
        });

        if (!is_null(config('stripe_payment.stripe_updatable'))) {
            $this->app->bind(StripeUpdatable::class, config('stripe_payment.stripe_updatable'));
        }
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
