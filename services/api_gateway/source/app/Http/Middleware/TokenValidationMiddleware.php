<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Microservices;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenValidationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Пропускаем валидацию токена для /users/login и /users/register
        if ($this->isPublicRoute($request)) {
            return $next($request);
        }

        // Извлекаем токен из заголовка запроса
        $token = $request->bearerToken();

        // Если токен отсутствует или он неверный
        if (!$token || !$this->isValidToken($token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    // Проверка, является ли путь публичным (не требует токена)
    private function isPublicRoute(Request $request): bool
    {
        return in_array($request->path(), Microservices::getAllPublicRoutes());
    }

    // Логика для валидации токена
    private function isValidToken(string $token): bool
    {
        $usersMicroserviceUrl = Microservices::getUsersMicroserviceUrl();
        $validateTokenUrl = $usersMicroserviceUrl . '/api/validate-token';

        // Отправляем запрос на сервис users для проверки токена
        try {
            $response = Http::withToken($token)->get($validateTokenUrl);

            if ($response->failed()) {
                return false;
            }

            return $response->status() === 200;
        } catch (\Exception $e) {
            // Логируем ошибку и возвращаем false, если что-то пошло не так
            Log::error('Token validation failed: ' . $e->getMessage());
            return false;
        }
    }
}
