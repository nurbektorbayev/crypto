<?php

declare(strict_types=1);

namespace App\Transport\Requests\Auth;

use App\Transport\Requests\RabbitMQRequest;

class RegisterWithTelegramRequest extends RabbitMQRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2',
            'telegram_id' => 'required|string',  // ID Telegram пользователя
        ];
    }
}
