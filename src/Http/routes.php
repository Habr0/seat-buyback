<?php

use Habr0\Buyback\Http\Controllers\AdminController;
use Habr0\Buyback\Http\Controllers\AppraisalController;
use Habr0\Buyback\Http\Controllers\ContractsController;
use Illuminate\Support\Facades\Route;

Route::controller(AppraisalController::class)
    ->middleware(['web', 'auth', 'locale', 'can:buyback.appraisals'])
    ->prefix('buyback')
    ->group(function () {
        Route::get('/appraisal', 'index')->name('buyback.appraisal_index');
        Route::post('/appraisal', 'store')->name('buyback.appraisal_store');
    });

Route::controller(ContractsController::class)
    ->middleware(['web', 'auth', 'locale', 'can:buyback.contracts'])
    ->prefix('buyback')
    ->group(function () {
        Route::get('/contracts', 'index')->name('buyback.contracts_index');
    });

Route::controller(AdminController::class)
    ->middleware(['web', 'auth', 'locale', 'can:buyback.admin'])
    ->prefix('buyback')
    ->group(function () {
        Route::get('/admin', 'index')->name('buyback.admin_index');
        Route::post('/admin/save', 'update')->name('buyback.admin_update');

        Route::get('/system-search', 'systemSearch')->name('buyback.admin_system_search');

        Route::get('/admin/price-modifier', 'indexPriceModifier')->name('buyback.admin_market_group_index');
        Route::post('/admin/price-modifier', 'storePriceModifier')->name('buyback.admin_market_group_store');
        Route::delete('/admin/price-modifier', 'deletePriceModifier')->name('buyback.admin_market_group_delete');
        Route::put('/admin/price-modifier', 'updatePriceModifier')->name('buyback.admin_market_group_update');
    });
