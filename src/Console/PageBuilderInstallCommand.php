<?php

namespace HolartWeb\HolartCMS\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use HolartWeb\HolartCMS\Models\TAdminAction;

class PageBuilderInstallCommand extends Command
{
    protected $signature = 'holartcms:pagebuilder-install';
    protected $description = 'Install HolartCMS Page Builder Module';

    public function handle(): int
    {
        $this->info('╔═══════════════════════════════════════════╗');
        $this->info('║ HolartCMS Page Builder Module Installer  ║');
        $this->info('╚═══════════════════════════════════════════╝');
        $this->newLine();

        // Determine package path (works for both local development and composer installation)
        $packagePath = base_path('vendor/holartweb/holart-cms');
        if (!file_exists($packagePath)) {
            $packagePath = base_path('packages/holartweb/holart-cms');
        }

        // Step 1: Copy Models
        $this->info('Step 1: Copying Page Builder models...');
        $appModelsPath = app_path('Models');

        $models = ['TPage.php', 'TPageBlock.php', 'TPageBlockType.php'];
        foreach ($models as $model) {
            $source = $packagePath . '/src/Models/Pages/' . $model;
            $destination = $appModelsPath . '/' . $model;

            if (file_exists($source)) {
                $content = file_get_contents($source);
                // Update namespace from package to app
                $content = str_replace(
                    'namespace HolartWeb\HolartCMS\Models\Pages;',
                    'namespace App\Models;',
                    $content
                );
                file_put_contents($destination, $content);
                $this->info("✓ Copied {$model}");
            }
        }
        $this->newLine();

        // Step 2: Copy Controllers
        $this->info('Step 2: Copying Page Builder controllers...');
        $appControllersPath = app_path('Http/Controllers');

        $controllers = ['PagesController.php', 'PageBlocksController.php', 'PageBlockTypesController.php', 'PageBlockFieldsController.php'];
        foreach ($controllers as $controller) {
            $source = $packagePath . '/src/Http/Controllers/Pages/' . $controller;
            $destination = $appControllersPath . '/' . $controller;

            if (file_exists($source)) {
                $content = file_get_contents($source);
                // Update namespace from package to app
                $content = str_replace(
                    'namespace HolartWeb\HolartCMS\Http\Controllers\Pages;',
                    'namespace App\Http\Controllers;',
                    $content
                );
                $content = str_replace(
                    'use HolartWeb\HolartCMS\Models\Pages\\',
                    'use App\Models\\',
                    $content
                );
                file_put_contents($destination, $content);
                $this->info("✓ Copied {$controller}");
            }
        }
        $this->newLine();

        // Step 3: Run migrations
        $this->info('Step 3: Running Page Builder migrations...');

        // Determine migration path
        $migrationPath = 'vendor/holartweb/holart-cms/database/migrations/pages';
        if (!file_exists(base_path($migrationPath))) {
            $migrationPath = 'packages/holartweb/holart-cms/database/migrations/pages';
        }

        Artisan::call('migrate', [
            '--path' => $migrationPath,
            '--force' => true
        ]);
        $this->info('✓ Migrations completed');
        $this->newLine();

        $this->info('╔═══════════════════════════════════════════╗');
        $this->info('║ Page Builder Module installed successfully ║');
        $this->info('╚═══════════════════════════════════════════╝');

        // Log activity if logging module is installed
        if (Schema::hasTable('t_admin_actions') && class_exists(TAdminAction::class)) {
            TAdminAction::log('installed', 'module', null, 'Установлен модуль: Конструктор страниц');
        }

        return Command::SUCCESS;
    }
}
