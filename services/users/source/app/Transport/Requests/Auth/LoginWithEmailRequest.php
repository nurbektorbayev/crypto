<?php

declare(strict_types=1);

namespace App\Transport\Requests\Auth;

use App\Transport\Requests\RabbitMQRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class LoginWithEmailRequest extends RabbitMQRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required'
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after([$this, 'validateUserExists']);
    }

    public function validateUserExists(Validator $validator): void
    {
        if (!Auth::attempt($this->only(['email', 'password']))) {
            $validator->errors()->add('email', __('Invalid email or password'));
            $validator->errors()->add('password', __('Invalid email or password'));
        }
    }
}
