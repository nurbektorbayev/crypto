<?php

use App\Http\Controllers\ProxyController;
use App\Http\Middleware\TokenValidationMiddleware;
use Illuminate\Support\Facades\Route;

Route::any('{service}/{any?}', [ProxyController::class, 'forward'])
    ->where('any', '.*')
    ->middleware(TokenValidationMiddleware::class);
