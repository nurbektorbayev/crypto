<?php

declare(strict_types=1);

namespace App\Transport\Requests\Auth;

use App\Transport\Requests\RabbitMQRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class LoginWithTelegramRequest extends RabbitMQRequest
{
    public function rules(): array
    {
        return [
            'telegram_id' => 'required|string',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after([$this, 'validateUserExists']);
    }

    public function validateUserExists(Validator $validator): void
    {
        if (!Auth::attempt($this->only(['telegram_id']))) {
            $validator->errors()->add('telegram_id', __('Invalid telegram_id'));
        }
    }
}
