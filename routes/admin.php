<?php

use Illuminate\Support\Facades\Route;
use HolartWeb\HolartCMS\Http\Controllers\Auth\LoginController;
use HolartWeb\HolartCMS\Http\Controllers\Auth\ForgotPasswordController;
use HolartWeb\HolartCMS\Http\Controllers\DashboardController;
use HolartWeb\HolartCMS\Http\Controllers\AdministratorController;
use HolartWeb\HolartCMS\Http\Controllers\SettingsController;

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
        Route::get('administrators', [AdministratorController::class, 'index']);
        Route::post('administrators', [AdministratorController::class, 'store']);
        Route::put('administrators/{id}', [AdministratorController::class, 'update']);
        Route::delete('administrators/{id}', [AdministratorController::class, 'destroy']);

        Route::get('settings', [SettingsController::class, 'index']);
        Route::post('settings', [SettingsController::class, 'update']);
    });

    // SPA route - catch all for Vue Router
    Route::get('/{any}', [DashboardController::class, 'index'])
        ->where('any', '.*')
        ->name('holart-cms.spa');
});
