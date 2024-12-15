<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * Public API
 */
Route::prefix('')
    ->group(function(Router $router) {
        /**
         * AUTH
         */
        $router->post('/auth/register-via-telegram', [AuthController::class, 'registerWithTelegram']);
        $router->post('/auth/register-via-email', [AuthController::class, 'registerWithEmail']);
        $router->post('/auth/login-via-telegram', [AuthController::class, 'loginWithTelegram']);
        $router->post('/auth/login-via-email', [AuthController::class, 'loginWithEmail']);
    });
