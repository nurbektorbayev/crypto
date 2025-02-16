<?php

declare(strict_types=1);

namespace App\Transport\Requests\User;

use App\Transport\Requests\RabbitMQRequest;

class GetUserRequest extends RabbitMQRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:users,id',
        ];
    }
}
