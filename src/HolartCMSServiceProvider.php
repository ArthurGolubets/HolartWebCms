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

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'holart-cms');

        // Register middleware
        $this->app['router']->aliasMiddleware('admin.auth', \HolartWeb\HolartCMS\Http\Middleware\RedirectIfNotAdmin::class);

        // Register commands (always register so they can be called via Artisan::call() from web)
        $this->commands([
            \HolartWeb\HolartCMS\Console\InstallCommand::class,
            \HolartWeb\HolartCMS\Console\UpdateCommand::class,
            \HolartWeb\HolartCMS\Console\ShopInstallCommand::class,
            \HolartWeb\HolartCMS\Console\ShopUninstallCommand::class,
            \HolartWeb\HolartCMS\Console\CallbackInstallCommand::class,
            \HolartWeb\HolartCMS\Console\CallbackUninstallCommand::class,
            \HolartWeb\HolartCMS\Console\CommerceInstallCommand::class,
            \HolartWeb\HolartCMS\Console\CommerceUninstallCommand::class,
        ]);

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
