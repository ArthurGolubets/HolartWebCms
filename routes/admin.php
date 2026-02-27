<?php

use Illuminate\Support\Facades\Route;
use HolartWeb\HolartCMS\Http\Controllers\Auth\LoginController;
use HolartWeb\HolartCMS\Http\Controllers\Auth\ForgotPasswordController;
use HolartWeb\HolartCMS\Http\Controllers\DashboardController;
use HolartWeb\HolartCMS\Http\Controllers\AdministratorController;
use HolartWeb\HolartCMS\Http\Controllers\SettingsController;
use HolartWeb\HolartCMS\Http\Controllers\SearchController;
use HolartWeb\HolartCMS\Http\Controllers\CatalogImportExportController;
use HolartWeb\HolartCMS\Http\Controllers\ProductImportExportController;
use HolartWeb\HolartCMS\Http\Controllers\LogsController;
use HolartWeb\HolartCMS\Http\Controllers\ModulesController;
use HolartWeb\HolartCMS\Http\Controllers\EnvironmentController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here are the admin panel routes for HolartCMS
|
*/

// Guest routes (not authenticated)
Route::middleware('guest:admin')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('holart-cms.login');
    Route::post('login', [LoginController::class, 'login'])->name('holart-cms.login.post');

    // Password Reset Routes
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('holart-cms.password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('holart-cms.password.email');
});

// Authenticated admin routes
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('holart-cms.dashboard');
    Route::post('logout', [LoginController::class, 'logout'])->name('holart-cms.logout');

    // API Routes
    Route::prefix('api')->group(function () {
        Route::get('me', [DashboardController::class, 'me']);
        Route::get('search', [SearchController::class, 'search']);

        Route::get('administrators', [AdministratorController::class, 'index']);
        Route::post('administrators', [AdministratorController::class, 'store']);
        Route::put('administrators/{id}', [AdministratorController::class, 'update']);
        Route::delete('administrators/{id}', [AdministratorController::class, 'destroy']);

        Route::get('settings', [SettingsController::class, 'index']);
        Route::post('settings', [SettingsController::class, 'update']);

        Route::get('environment', [EnvironmentController::class, 'index']);
        Route::post('environment', [EnvironmentController::class, 'update']);
        Route::post('environment/test-smtp', [EnvironmentController::class, 'testSmtp']);

        Route::get('logs', [LogsController::class, 'index']);
        Route::get('logs/actions', [LogsController::class, 'actions']);
        Route::get('logs/entity-types', [LogsController::class, 'entityTypes']);

        Route::get('modules', [ModulesController::class, 'index']);
        Route::post('modules/update', [ModulesController::class, 'update']);
        Route::post('modules/{moduleId}/install', [ModulesController::class, 'install']);
        Route::post('modules/{moduleId}/uninstall', [ModulesController::class, 'uninstall']);

        // Catalog routes - only if shop module is installed
        $catalogControllerPath = app_path('Http/Controllers/CatalogController.php');
        if (file_exists($catalogControllerPath)) {
            $catalogController = 'App\\Http\\Controllers\\CatalogController';
            $productController = 'App\\Http\\Controllers\\ProductController';

            // Catalog Import/Export routes - MUST come before generic routes
            Route::get('catalogs/import-template', [CatalogImportExportController::class, 'downloadTemplate']);
            Route::get('catalogs/export', [CatalogImportExportController::class, 'export']);
            Route::post('catalogs/import-preview', [CatalogImportExportController::class, 'previewImport']);
            Route::post('catalogs/import', [CatalogImportExportController::class, 'import']);
            Route::get('catalogs/tree', [$catalogController, 'tree']);
            Route::get('catalogs/list', [$catalogController, 'list']);

            // Product Import/Export routes - MUST come before generic routes
            Route::get('products/import-template', [ProductImportExportController::class, 'downloadTemplate']);
            Route::get('products/export', [ProductImportExportController::class, 'export']);
            Route::post('products/import-preview', [ProductImportExportController::class, 'previewImport']);
            Route::post('products/import', [ProductImportExportController::class, 'import']);
            Route::post('products/bulk-delete', [$productController, 'bulkDestroy']);

            // Catalog generic routes
            Route::get('catalogs', [$catalogController, 'index']);
            Route::get('catalogs/{id}', [$catalogController, 'show']);
            Route::post('catalogs', [$catalogController, 'store']);
            Route::put('catalogs/{id}', [$catalogController, 'update']);
            Route::delete('catalogs/{id}', [$catalogController, 'destroy']);
            Route::get('catalogs/{id}/children', [$catalogController, 'children']);

            // Product generic routes
            Route::get('products', [$productController, 'index']);
            Route::get('products/{id}', [$productController, 'show']);
            Route::post('products', [$productController, 'store']);
            Route::put('products/{id}', [$productController, 'update']);
            Route::delete('products/{id}', [$productController, 'destroy']);
        }
    });

    // SPA route - catch all for Vue Router
    Route::get('/{any}', [DashboardController::class, 'index'])
        ->where('any', '.*')
        ->name('holart-cms.spa');
});
