<?php

namespace HolartWeb\HolartCMS\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShopUninstallCommand extends Command
{
    protected $signature = 'holartcms:shop-uninstall {--preserve-db : Preserve database tables and data}';
    protected $description = 'Uninstall HolartCMS Shop Module';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║  HolartCMS Shop Module Uninstaller  ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        $preserveDb = $this->option('preserve-db');

        if (!$preserveDb) {
            $this->warn('⚠ WARNING: This will delete all shop data from the database!');
            if (!$this->confirm('Are you sure you want to continue?', false)) {
                $this->info('Uninstallation cancelled.');
                return self::SUCCESS;
            }
        }

        // Step 1: Remove Models
        $this->info('Step 1: Removing shop models...');
        $appModelsPath = app_path('Models');
        $models = ['TCatalog.php', 'TProduct.php', 'TProductVariant.php'];

        foreach ($models as $model) {
            $path = $appModelsPath . '/' . $model;
            if (File::exists($path)) {
                File::delete($path);
                $this->info("✓ Removed {$model}");
            }
        }
        $this->newLine();

        // Step 2: Remove Controllers
        $this->info('Step 2: Removing shop controllers...');
        $appControllersPath = app_path('Http/Controllers');
        $controllers = ['CatalogController.php', 'ProductController.php'];

        foreach ($controllers as $controller) {
            $path = $appControllersPath . '/' . $controller;
            if (File::exists($path)) {
                File::delete($path);
                $this->info("✓ Removed {$controller}");
            }
        }
        $this->newLine();

        // Step 3: Handle Database
        if (!$preserveDb) {
            $this->info('Step 3: Removing database tables...');

            try {
                Schema::disableForeignKeyConstraints();

                if (Schema::hasTable('t_product_variants')) {
                    Schema::dropIfExists('t_product_variants');
                    $this->info('✓ Dropped t_product_variants table');
                }

                if (Schema::hasTable('t_products')) {
                    Schema::dropIfExists('t_products');
                    $this->info('✓ Dropped t_products table');
                }

                if (Schema::hasTable('t_catalogs')) {
                    Schema::dropIfExists('t_catalogs');
                    $this->info('✓ Dropped t_catalogs table');
                }

                Schema::enableForeignKeyConstraints();
            } catch (\Exception $e) {
                $this->error('❌ Error removing database tables: ' . $e->getMessage());
            }
            $this->newLine();

            // Step 4: Remove Migrations
            $this->info('Step 4: Removing migration files...');
            $migrationFiles = [
                '2024_01_01_000010_create_t_catalogs_table.php',
                '2024_01_01_000011_create_t_products_table.php',
                '2024_01_01_000012_create_t_product_variants_table.php',
                '2024_01_01_000013_add_main_image_to_products.php',
            ];

            foreach ($migrationFiles as $file) {
                $path = database_path('migrations/' . $file);
                if (File::exists($path)) {
                    File::delete($path);
                    $this->info("✓ Removed migration {$file}");
                }
            }
            $this->newLine();
        } else {
            $this->info('Step 3: Preserving database tables and data...');
            $this->info('✓ Database preserved');
            $this->newLine();
        }

        // Step 5: Clear Cache
        $this->info('Step 5: Clearing application cache...');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->info('✓ Cache cleared successfully');
        $this->newLine();

        // Success Message
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║ Shop Module Uninstalled Successfully! ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        if ($preserveDb) {
            $this->warn('Database tables were preserved. To completely remove shop data,');
            $this->warn('run the command again without the --preserve-db option.');
        }

        return self::SUCCESS;
    }
}
