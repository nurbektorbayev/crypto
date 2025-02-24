<?php

declare(strict_types=1);

namespace App\Transport\Requests\Auth;

use App\Transport\Requests\RabbitMQRequest;
use Illuminate\Support\Facades\Auth;

class LoginWithEmailRequest extends RabbitMQRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', function ($attribute, $value, $fail) {
                if (!Auth::attempt($this->only(['email', 'password']))) {
                    $fail(__('Invalid email or password'));
                }
            }]
        ];
    }
}
