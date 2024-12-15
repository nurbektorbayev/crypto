<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function __construct(private string $botApiUrl)
    {
    }

    public function authenticateWithTelegram(string $code): array
    {
        // Здесь должен быть код для аутентификации пользователя через Telegram.
        // Возвращаем фейковый ответ для примера.
        return [
            'status' => 'success',
            'user_id' => 12345,  // Пример ID пользователя
            'username' => 'telegram_user',
        ];
    }

    public function sendMessage(int $chatId, string $message): bool
    {
        $response = Http::post($this->botApiUrl . 'sendMessage', [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        return $response->successful();
    }
}
