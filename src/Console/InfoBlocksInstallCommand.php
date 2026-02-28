<?php

namespace HolartWeb\HolartCMS\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InfoBlocksInstallCommand extends Command
{
    protected $signature = 'holartcms:infoblocks-install';
    protected $description = 'Install HolartCMS InfoBlocks Module';

    public function handle(): int
    {
        $this->info('╔════════════════════════════════════════╗');
        $this->info('║ HolartCMS InfoBlocks Module Installer ║');
        $this->info('╚════════════════════════════════════════╝');
        $this->newLine();

        // Determine package path (works for both local development and composer installation)
        $packagePath = base_path('vendor/holartweb/holart-cms');
        if (!file_exists($packagePath)) {
            $packagePath = base_path('packages/holartweb/holart-cms');
        }

        // Step 1: Copy InfoBlocks Models
        $this->info('Step 1: Copying infoblocks models...');
        $appModelsPath = app_path('Models');

        $models = ['TInfoBlock.php', 'TInfoBlockField.php', 'TInfoBlockElement.php'];
        foreach ($models as $model) {
            $source = $packagePath . '/src/Models/InfoBlocks/' . $model;
            $destination = $appModelsPath . '/' . $model;

            if (file_exists($source)) {
                $content = file_get_contents($source);
                // Update namespace from package to app
                $content = str_replace(
                    'namespace HolartWeb\HolartCMS\Models\InfoBlocks;',
                    'namespace App\Models;',
                    $content
                );
                file_put_contents($destination, $content);
                $this->info("✓ Copied {$model}");
            } else {
                $this->warn("⚠ Source file not found: {$source}");
            }
        }
        $this->newLine();

        // Step 2: Copy TInfoBlock Helper
        $this->info('Step 2: Copying TInfoBlock helper...');
        $appHelpersPath = app_path('Helpers');

        // Create Helpers directory if it doesn't exist
        if (!file_exists($appHelpersPath)) {
            mkdir($appHelpersPath, 0755, true);
        }

        $source = $packagePath . '/src/Helpers/TInfoBlock.php';
        $destination = $appHelpersPath . '/TInfoBlock.php';

        if (file_exists($source)) {
            $content = file_get_contents($source);
            // Update namespace from package to app
            $content = str_replace(
                'namespace HolartWeb\HolartCMS\Helpers;',
                'namespace App\Helpers;',
                $content
            );
            $content = str_replace(
                'use HolartWeb\HolartCMS\Models\InfoBlocks\TInfoBlock as TInfoBlockModel;',
                'use App\Models\TInfoBlock as TInfoBlockModel;',
                $content
            );
            $content = str_replace(
                'use HolartWeb\HolartCMS\Models\InfoBlocks\TInfoBlockElement;',
                'use App\Models\TInfoBlockElement;',
                $content
            );
            file_put_contents($destination, $content);
            $this->info("✓ Copied TInfoBlock.php");
        } else {
            $this->warn("⚠ Source file not found: {$source}");
        }
        $this->newLine();

        // Step 3: Copy InfoBlocks Controllers
        $this->info('Step 3: Copying infoblocks controllers...');
        $appControllersPath = app_path('Http/Controllers');

        $controllers = [
            'InfoBlocksController.php',
            'InfoBlockFieldsController.php',
            'InfoBlockElementsController.php'
        ];

        foreach ($controllers as $controller) {
            $source = $packagePath . '/src/Http/Controllers/InfoBlocks/' . $controller;
            $destination = $appControllersPath . '/' . $controller;

            if (file_exists($source)) {
                $content = file_get_contents($source);
                // Update namespace from package to app
                $content = str_replace(
                    'namespace HolartWeb\HolartCMS\Http\Controllers\InfoBlocks;',
                    'namespace App\Http\Controllers;',
                    $content
                );
                $content = str_replace(
                    'use Illuminate\Routing\Controller;',
                    '',
                    $content
                );
                $content = str_replace(
                    'use HolartWeb\HolartCMS\Models\InfoBlocks\TInfoBlock;',
                    'use App\Models\TInfoBlock;',
                    $content
                );
                $content = str_replace(
                    'use HolartWeb\HolartCMS\Models\InfoBlocks\TInfoBlockField;',
                    'use App\Models\TInfoBlockField;',
                    $content
                );
                $content = str_replace(
                    'use HolartWeb\HolartCMS\Models\InfoBlocks\TInfoBlockElement;',
                    'use App\Models\TInfoBlockElement;',
                    $content
                );
                file_put_contents($destination, $content);
                $this->info("✓ Copied {$controller}");
            } else {
                $this->warn("⚠ Source file not found: {$source}");
            }
        }
        $this->newLine();

        // Step 4: Copy and Run Migrations
        $this->info('Step 4: Copying and running database migrations...');
        $migrationFiles = [
            '2024_01_01_000040_create_t_info_blocks_table.php',
            '2024_01_01_000041_create_t_info_block_fields_table.php',
            '2024_01_01_000042_create_t_info_block_elements_table.php',
        ];

        foreach ($migrationFiles as $file) {
            $source = $packagePath . '/database/migrations/infoblocks/' . $file;
            $destination = database_path('migrations/' . $file);

            if (file_exists($source)) {
                copy($source, $destination);
                $this->info("✓ Copied migration {$file}");
            } else {
                $this->warn("⚠ Source migration not found: {$source}");
            }
        }

        try {
            // Run only infoblocks module migrations
            foreach ($migrationFiles as $file) {
                $migrationPath = database_path('migrations/' . $file);
                if (file_exists($migrationPath)) {
                    Artisan::call('migrate', [
                        '--path' => 'database/migrations/' . $file,
                        '--force' => true
                    ]);
                }
            }
            $this->info('✓ Migrations completed successfully');
        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            return self::FAILURE;
        }
        $this->newLine();

        // Step 5: Build Frontend Assets
        $this->info('Step 5: Building frontend assets...');

        if (file_exists($packagePath . '/package.json')) {
            $this->info('Installing npm dependencies...');
            exec("cd {$packagePath} && npm install 2>&1", $output, $returnVar);

            if ($returnVar !== 0) {
                $this->warn('⚠ npm install encountered issues');
            } else {
                $this->info('✓ npm dependencies installed');
            }

            $this->info('Building assets...');
            exec("cd {$packagePath} && npm run build 2>&1", $output, $returnVar);

            if ($returnVar !== 0) {
                $this->error('❌ Asset build failed');
                return self::FAILURE;
            }
            $this->info('✓ Assets built successfully');
        } else {
            $this->warn('⚠ package.json not found, skipping asset build');
        }
        $this->newLine();

        // Step 6: Publish Assets
        $this->info('Step 6: Publishing assets...');
        Artisan::call('vendor:publish', [
            '--tag' => 'holart-cms-assets',
            '--force' => true,
        ]);
        $this->info('✓ Assets published successfully');
        $this->newLine();

        // Step 7: Clear Cache
        $this->info('Step 7: Clearing application cache...');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->info('✓ Cache cleared successfully');
        $this->newLine();

        // Success Message
        $this->info('╔════════════════════════════════════════╗');
        $this->info('║InfoBlocks Module Installed Successfully!║');
        $this->info('╚════════════════════════════════════════╝');
        $this->newLine();
        $this->info('You can now create custom info blocks in your admin panel.');
        $this->info('Navigate to: ' . url('/admin/infoblocks'));
        $this->newLine();

        return self::SUCCESS;
    }
}
