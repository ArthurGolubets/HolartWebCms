<?php

namespace HolartWeb\HolartCMS\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use HolartWeb\HolartCMS\Models\TAdminAction;

class SeoInstallCommand extends Command
{
    protected $signature = 'holartcms:seo-install';
    protected $description = 'Install HolartCMS SEO Module';

    public function handle(): int
    {
        $this->info('╔═══════════════════════════════════════════╗');
        $this->info('║ HolartCMS Pages & SEO Module Installer   ║');
        $this->info('╚═══════════════════════════════════════════╝');
        $this->newLine();

        // Determine package path
        $packagePath = base_path('vendor/holartweb/holart-cms');
        if (!file_exists($packagePath)) {
            $packagePath = base_path('packages/holartweb/holart-cms');
        }

        // Step 1: Copy Models
        $this->info('Step 1: Copying SEO models...');
        $appModelsPath = app_path('Models');

        $models = ['TPage.php', 'TPageVisit.php'];
        foreach ($models as $model) {
            $source = $packagePath . '/src/Models/SEO/' . $model;
            $destination = $appModelsPath . '/' . $model;

            if (file_exists($source)) {
                $content = file_get_contents($source);
                // Update namespace from package to app
                $content = str_replace(
                    'namespace HolartWeb\HolartCMS\Models\SEO;',
                    'namespace App\Models;',
                    $content
                );
                file_put_contents($destination, $content);
                $this->info("✓ Copied {$model}");
            }
        }
        $this->newLine();

        // Step 2: Copy Controllers
        $this->info('Step 2: Copying SEO controllers...');
        $appControllersPath = app_path('Http/Controllers');

        $controllers = ['PagesController.php', 'PageStatsController.php'];
        foreach ($controllers as $controller) {
            $source = $packagePath . '/src/Http/Controllers/SEO/' . $controller;
            $destination = $appControllersPath . '/' . $controller;

            if (file_exists($source)) {
                $content = file_get_contents($source);
                // Update namespace from package to app
                $content = str_replace(
                    'namespace HolartWeb\HolartCMS\Http\Controllers\SEO;',
                    'namespace App\Http\Controllers;',
                    $content
                );
                // Replace model imports
                $content = str_replace(
                    'use HolartWeb\HolartCMS\Models\SEO\\',
                    'use App\Models\\',
                    $content
                );
                // Also replace individual model imports
                $content = str_replace(
                    'use HolartWeb\HolartCMS\Models\SEO\TPage;',
                    'use App\Models\TPage;',
                    $content
                );
                $content = str_replace(
                    'use HolartWeb\HolartCMS\Models\SEO\TPageVisit;',
                    'use App\Models\TPageVisit;',
                    $content
                );
                file_put_contents($destination, $content);
                $this->info("✓ Copied {$controller}");
            }
        }
        $this->newLine();

        // Step 3: Copy Service
        $this->info('Step 3: Copying PageVisitService...');
        $appServicesPath = app_path('Services');
        if (!file_exists($appServicesPath)) {
            mkdir($appServicesPath, 0755, true);
        }

        $source = $packagePath . '/src/Services/PageVisitService.php';
        $destination = $appServicesPath . '/PageVisitService.php';
        if (file_exists($source)) {
            $content = file_get_contents($source);
            $content = str_replace(
                'namespace HolartWeb\HolartCMS\Services;',
                'namespace App\Services;',
                $content
            );
            $content = str_replace(
                'use HolartWeb\HolartCMS\Models\SEO\\',
                'use App\Models\\',
                $content
            );
            file_put_contents($destination, $content);
            $this->info('✓ Copied PageVisitService.php');
        }
        $this->newLine();

        // Step 4: Copy Middleware
        $this->info('Step 4: Copying TrackPageVisits middleware...');
        $appMiddlewarePath = app_path('Http/Middleware');
        if (!file_exists($appMiddlewarePath)) {
            mkdir($appMiddlewarePath, 0755, true);
        }

        $source = $packagePath . '/src/Http/Middleware/TrackPageVisits.php';
        $destination = $appMiddlewarePath . '/TrackPageVisits.php';
        if (file_exists($source)) {
            $content = file_get_contents($source);
            $content = str_replace(
                'namespace HolartWeb\HolartCMS\Http\Middleware;',
                'namespace App\Http\Middleware;',
                $content
            );
            $content = str_replace(
                'use HolartWeb\HolartCMS\Services\PageVisitService;',
                'use App\Services\PageVisitService;',
                $content
            );
            file_put_contents($destination, $content);
            $this->info('✓ Copied TrackPageVisits.php');
        }
        $this->newLine();

        // Step 5: Run migrations
        $this->info('Step 5: Running SEO migrations...');

        // Determine migration path
        $migrationPath = 'vendor/holartweb/holart-cms/database/migrations/seo';
        if (!file_exists(base_path($migrationPath))) {
            $migrationPath = 'packages/holartweb/holart-cms/database/migrations/seo';
        }

        Artisan::call('migrate', [
            '--path' => $migrationPath,
            '--force' => true
        ]);
        $this->info('✓ Migrations completed');
        $this->newLine();

        // Step 6: Register middleware automatically
        $this->info('Step 6: Registering middleware...');
        $this->registerMiddleware();
        $this->info('✓ Middleware registered');
        $this->newLine();

        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║ Pages & SEO Module installed successfully     ║');
        $this->info('╚════════════════════════════════════════════════╝');

        // Log activity if logging module is installed
        if (Schema::hasTable('t_admin_actions') && class_exists(TAdminAction::class)) {
            TAdminAction::log('installed', 'module', null, 'Установлен модуль: Страницы и SEO');
        }

        return Command::SUCCESS;
    }

    /**
     * Register middleware in bootstrap/app.php
     */
    private function registerMiddleware()
    {
        $bootstrapPath = base_path('bootstrap/app.php');

        if (!file_exists($bootstrapPath)) {
            $this->warn('⚠ bootstrap/app.php not found. Please register middleware manually.');
            return;
        }

        $content = file_get_contents($bootstrapPath);
        $middlewareClass = '\App\Http\Middleware\TrackPageVisits::class';

        // Check if already registered
        if (str_contains($content, 'TrackPageVisits')) {
            $this->info('   Middleware already registered');
            return;
        }

        // For Laravel 11+ style bootstrap/app.php
        if (str_contains($content, '->withMiddleware')) {
            // Try different patterns for withMiddleware including void return type
            $patterns = [
                '/->withMiddleware\(function\s*\(\s*Middleware\s+\$middleware\s*\)\s*:\s*void\s*{/',
                '/->withMiddleware\(function\s*\(\s*Middleware\s+\$middleware\s*\)\s*{/',
                '/->withMiddleware\(function\s*\(\s*\$middleware\s*\)\s*{/',
                '/->withMiddleware\(\s*function\s*\(\s*Middleware\s+\$middleware\s*\)\s*{/',
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    $insertPosition = $matches[0][1] + strlen($matches[0][0]);

                    // Insert the middleware registration
                    $middlewareCode = "\n        \$middleware->web(append: [\n            {$middlewareClass},\n        ]);\n";

                    $content = substr_replace($content, $middlewareCode, $insertPosition, 0);
                    file_put_contents($bootstrapPath, $content);
                    $this->info('   Middleware registered successfully');
                    return;
                }
            }
        }

        // Fallback: couldn't auto-register
        $this->warn('⚠ Could not auto-register middleware. Please add manually to bootstrap/app.php:');
        $this->warn('   $middleware->web(append: [');
        $this->warn('       ' . $middlewareClass . ',');
        $this->warn('   ]);');
    }
}
