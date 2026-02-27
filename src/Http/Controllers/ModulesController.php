<?php

namespace HolartWeb\HolartCMS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class ModulesController extends Controller
{
    /**
     * Get list of available modules with their status
     */
    public function index()
    {
        $modules = [
            [
                'id' => 'shop',
                'name' => 'Каталог и товары',
                'description' => 'Модуль, который добавит на сайт систему каталогов, товаров, и их логику',
                'installed' => $this->isShopModuleInstalled(),
                'install_command' => 'holartcms:shop-install',
                'uninstall_command' => 'holartcms:shop-uninstall',
            ]
        ];

        return response()->json([
            'modules' => $modules
        ]);
    }

    /**
     * Execute system update command
     */
    public function update(Request $request)
    {
        try {
            Artisan::call('holartcms:update');

            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output,
                'message' => 'Обновление выполнено успешно'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Install a module
     */
    public function install(Request $request, $moduleId)
    {
        $request->validate([
            'module_id' => 'required|string'
        ]);

        try {
            switch ($moduleId) {
                case 'shop':
                    Artisan::call('holartcms:shop-install');
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Неизвестный модуль'
                    ], 404);
            }

            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output,
                'message' => 'Модуль установлен успешно'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при установке модуля: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Uninstall a module
     */
    public function uninstall(Request $request, $moduleId)
    {
        $request->validate([
            'preserve_database' => 'boolean'
        ]);

        $preserveDatabase = $request->input('preserve_database', false);

        try {
            switch ($moduleId) {
                case 'shop':
                    Artisan::call('holartcms:shop-uninstall', [
                        '--preserve-db' => $preserveDatabase
                    ]);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Неизвестный модуль'
                    ], 404);
            }

            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output,
                'message' => 'Модуль удален успешно'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении модуля: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if shop module is installed
     */
    private function isShopModuleInstalled()
    {
        // Check if catalog and product controllers exist (they are copied during install)
        return file_exists(app_path('Http/Controllers/CatalogController.php')) &&
               file_exists(app_path('Http/Controllers/ProductController.php')) &&
               Schema::hasTable('t_catalogs') &&
               Schema::hasTable('t_products');
    }

    /**
     * Get license key from settings
     */
    private function getLicenseKey()
    {
        if (class_exists('HolartWeb\HolartCMS\Models\TPanelSettings')) {
            $settings = \HolartWeb\HolartCMS\Models\TPanelSettings::first();
            return $settings->license_key ?? '';
        }
        return '';
    }
}
