<?php

declare(strict_types=1);

use App\Http\Controllers\Public\AuthController;
use App\Http\Middleware\TokenValidationMiddleware;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/**
 * Public API
 */
Route::middleware([])
    ->group(function(Router $router) {
        /**
         * AUTH
         */
        $router->post('/auth/login-with-email', [AuthController::class, 'loginWithEmail']);


    });

/**
 * Private API
 */
Route::middleware([TokenValidationMiddleware::class])
    ->group(function(Router $router) {

    });
