<?php

namespace HolartWeb\HolartCMS;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class HolartCMSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge package config
        $this->mergeConfigFrom(
            __DIR__.'/../config/holart-cms.php', 'holart-cms'
        );

        // Register admin guard
        $this->app['config']->set('auth.guards.admin', [
            'driver' => 'session',
            'provider' => 'administrators',
        ]);

        $this->app['config']->set('auth.providers.administrators', [
            'driver' => 'eloquent',
            'model' => \HolartWeb\HolartCMS\Models\TAdministrator::class,
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'holart-cms');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \HolartWeb\HolartCMS\Console\InstallCommand::class,
            ]);
        }

        // Publish config
        $this->publishes([
            __DIR__.'/../config/holart-cms.php' => config_path('holart-cms.php'),
        ], 'holart-cms-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'holart-cms-migrations');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/holart-cms'),
        ], 'holart-cms-views');

        // Publish assets
        $this->publishes([
            __DIR__.'/../resources/dist' => public_path('vendor/holart-cms'),
        ], 'holart-cms-assets');

        // Register admin routes with prefix
        $this->registerAdminRoutes();
    }

    /**
     * Register admin routes.
     */
    protected function registerAdminRoutes(): void
    {
        Route::group([
            'prefix' => config('holart-cms.route_prefix', 'admin'),
            'middleware' => ['web'],
            'namespace' => 'HolartWeb\HolartCMS\Http\Controllers',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');
        });
    }
}
