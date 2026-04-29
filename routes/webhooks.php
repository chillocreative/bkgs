<?php

use App\Http\Controllers\Webhooks\BayarcashController;
use App\Http\Controllers\Webhooks\SendoraController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/bayarcash/callback', [BayarcashController::class, 'callback'])
    ->name('webhooks.bayarcash');

Route::post('/webhooks/sendora', [SendoraController::class, 'webhook'])
    ->name('webhooks.sendora');
