<?php

namespace Motive;

use Illuminate\Support\ServiceProvider;

/**
 * Laravel service provider for the Motive SDK.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/motive.php' => config_path('motive.php'),
            ], 'motive-config');
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/motive.php',
            'motive'
        );

        $this->app->singleton(MotiveManager::class, function ($app) {
            return new MotiveManager($app['config']['motive']);
        });

        $this->app->alias(MotiveManager::class, 'motive');
    }
}
