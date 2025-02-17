<?php

declare(strict_types = 1);

namespace App\Services;

use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TokenService
{
    /**
     * Метод для валидации токена.
     *
     * @param string $token
     * @return int|null Возвращает ID пользователя, если токен валиден, иначе null
     */
    public function getUserIdByToken(string $token): ?int
    {
        // Проверяем токен в базе данных через модель PersonalAccessToken
        try {
            // Ищем токен в базе данных
            /** @var PersonalAccessToken $personalAccessToken */
            $personalAccessToken = PersonalAccessToken::findToken($token);

            // Если токен найден, возвращаем ID пользователя
            if ($personalAccessToken) {
                return $personalAccessToken->tokenable_id; // ID пользователя, связанный с токеном
            }

            // Если токен не найден, возвращаем null
            return null;
        } catch (ModelNotFoundException $e) {
            // Если токен не найден в базе данных, возвращаем null
            return null;
        }
    }
}
