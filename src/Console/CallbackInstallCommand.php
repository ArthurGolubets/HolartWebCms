<?php

namespace HolartWeb\HolartCMS\Console;

use Illuminate\Console\Command;
use HolartWeb\HolartCMS\Services\LicenseService;
use Illuminate\Support\Facades\Artisan;

class CallbackInstallCommand extends Command
{
    protected $signature = 'holartcms:callback-user-install';
    protected $description = 'Install HolartCMS Callback Module';

    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        parent::__construct();
        $this->licenseService = $licenseService;
    }

    public function handle(): int
    {
        $this->info('╔════════════════════════════════════════╗');
        $this->info('║  HolartCMS Callback Module Installer  ║');
        $this->info('╚════════════════════════════════════════╝');
        $this->newLine();

        // Step 1: Check License
        $this->info('Step 1: Checking license...');
        if (!$this->checkLicense()) {
            $this->error('❌ License verification failed!');
            $this->error('Please contact support to obtain a valid license key.');
            return self::FAILURE;
        }
        $this->info('✓ License verified successfully');
        $this->newLine();

        // Step 2: Copy Callback Models
        $this->info('Step 2: Copying callback models...');
        $packagePath = base_path('packages/holartweb/holart-cms');
        $appModelsPath = app_path('Models');

        $models = ['TUsersEmails.php', 'TComments.php', 'TUserRequests.php'];
        foreach ($models as $model) {
            $source = $packagePath . '/src/Models/Callback/' . $model;
            $destination = $appModelsPath . '/' . $model;

            if (file_exists($source)) {
                copy($source, $destination);
                $this->info("✓ Copied {$model}");
            }
        }
        $this->newLine();

        // Step 3: Copy Callback Controllers
        $this->info('Step 3: Copying callback controllers...');
        $appControllersPath = app_path('Http/Controllers');

        $controllers = ['UsersEmailsController.php', 'CommentsController.php', 'UserRequestsController.php'];
        foreach ($controllers as $controller) {
            $source = $packagePath . '/src/Http/Controllers/Callback/' . $controller;
            $destination = $appControllersPath . '/' . $controller;

            if (file_exists($source)) {
                $content = file_get_contents($source);
                // Update namespace from package to app
                $content = str_replace(
                    'namespace HolartWeb\HolartCMS\Http\Controllers\Callback;',
                    'namespace App\Http\Controllers;',
                    $content
                );
                $content = str_replace(
                    'use Illuminate\Routing\Controller;',
                    '',
                    $content
                );
                file_put_contents($destination, $content);
                $this->info("✓ Copied {$controller}");
            }
        }
        $this->newLine();

        // Step 4: Copy and Run Migrations
        $this->info('Step 4: Copying and running database migrations...');
        $migrationFiles = [
            '2024_01_01_000020_create_t_users_emails_table.php',
            '2024_01_01_000021_create_t_comments_table.php',
            '2024_01_01_000022_create_t_user_requests_table.php',
        ];

        foreach ($migrationFiles as $file) {
            $source = $packagePath . '/database/migrations/callback/' . $file;
            $destination = database_path('migrations/' . $file);

            if (file_exists($source)) {
                copy($source, $destination);
                $this->info("✓ Copied migration {$file}");
            }
        }

        try {
            // Run only callback module migrations
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
        $this->info('║ Callback Module Installed Successfully! ║');
        $this->info('╚════════════════════════════════════════╝');
        $this->newLine();
        $this->info('You can now access the callback features in your admin panel.');
        $this->info('Navigate to: ' . url('/admin/callback'));
        $this->newLine();

        return self::SUCCESS;
    }

    protected function checkLicense(): bool
    {
        // Check if license key already exists
        $savedKey = $this->licenseService->getSavedLicense();

        if ($savedKey && $this->licenseService->checkLicense($savedKey, 'callback-install')) {
            return true;
        }

        // If running in console (not web), request new license key
        if ($this->input->isInteractive()) {
            $this->warn('No valid license key found.');
            $key = $this->ask('Please enter your license key');

            if (empty($key)) {
                return false;
            }

            // Validate license
            if (!$this->licenseService->checkLicense($key, 'callback-install')) {
                $this->error('Invalid license key!');
                return false;
            }

            // Save license
            $this->licenseService->saveLicense($key);
            return true;
        }

        // If not interactive (web call), just use saved license or skip
        if ($savedKey) {
            return true;
        }

        $this->warn('⚠ No license key found, but continuing installation...');
        return true;
    }
}
