<?php

use Botble\Braintree\Http\Controllers\BraintreeController;
use Illuminate\Support\Facades\Route;

Route::prefix('payment/braintree')
    ->name('payments.braintree.')
    ->group(function () {
        Route::post('webhook', [BraintreeController::class, 'webhook'])->name('webhook');

        Route::middleware(['web', 'core'])->group(function () {
            Route::get('success', [BraintreeController::class, 'success'])->name('success');
            Route::get('error', [BraintreeController::class, 'error'])->name('error');
            Route::get('braintreegettoken', [BraintreeController::class, 'gettoken'])->name('gettoken');
        });
    });
