<?php

namespace HolartWeb\HolartCMS\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class LoggingInstallCommand extends Command
{
    protected $signature = 'holartcms:logging-install';
    protected $description = 'Install HolartCMS Logging Module';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║  HolartCMS Logging Module Installer ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        // Step 1: Copy Logs Controller
        $this->info('Step 1: Copying logs controller...');
        $packagePath = base_path('packages/holartweb/holart-cms');
        $appControllersPath = app_path('Http/Controllers');

        $source = $packagePath . '/src/Http/Controllers/Logging/LogsController.php';
        $destination = $appControllersPath . '/LogsController.php';

        if (file_exists($source)) {
            $content = file_get_contents($source);
            // Update namespace from package to app
            $content = str_replace(
                'namespace HolartWeb\HolartCMS\Http\Controllers\Logging;',
                'namespace App\Http\Controllers;',
                $content
            );
            $content = str_replace(
                'use Illuminate\Routing\Controller;',
                '',
                $content
            );
            file_put_contents($destination, $content);
            $this->info("✓ Copied LogsController.php");
        }
        $this->newLine();

        // Step 2: Copy and Run Migration
        $this->info('Step 2: Copying and running database migration...');
        $migrationFile = '2026_02_27_125658_create_t_admin_actions_table.php';

        $source = $packagePath . '/database/migrations/' . $migrationFile;
        $destination = database_path('migrations/' . $migrationFile);

        if (file_exists($source)) {
            copy($source, $destination);
            $this->info("✓ Copied migration {$migrationFile}");
        }

        try {
            // Run only logging module migration
            $migrationPath = database_path('migrations/' . $migrationFile);
            if (file_exists($migrationPath)) {
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/' . $migrationFile,
                    '--force' => true
                ]);
            }
            $this->info('✓ Migrations completed successfully');
        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            return self::FAILURE;
        }
        $this->newLine();

        // Step 3: Build Frontend Assets
        $this->info('Step 3: Building frontend assets...');

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

        // Step 4: Publish Assets
        $this->info('Step 4: Publishing assets...');
        Artisan::call('vendor:publish', [
            '--tag' => 'holart-cms-assets',
            '--force' => true,
        ]);
        $this->info('✓ Assets published successfully');
        $this->newLine();

        // Step 5: Clear Cache
        $this->info('Step 5: Clearing application cache...');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->info('✓ Cache cleared successfully');
        $this->newLine();

        // Success Message
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║ Logging Module Installed Successfully! ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();
        $this->info('You can now track all admin actions in your system.');
        $this->info('Navigate to: ' . url('/admin/logs'));
        $this->newLine();

        return self::SUCCESS;
    }
}
