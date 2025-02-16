<?php

declare(strict_types=1);

namespace App\Transport\Requests\Auth;

use App\Transport\Requests\RabbitMQRequest;

class RegisterWithEmailRequest extends RabbitMQRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
