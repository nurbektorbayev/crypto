<?php

declare(strict_types=1);

namespace App\Transport\Requests\Auth;

use App\Services\TokenService;
use App\Transport\Requests\RabbitMQRequest;
use Illuminate\Validation\Validator;

class ValidateTokenRequest extends RabbitMQRequest
{
    public function rules(): array
    {
        return [
            'token' => 'required|string',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after([$this, 'validateToken']);
    }

    public function validateToken(Validator $validator): void
    {
        /** @var TokenService $tokenService */
        $tokenService = app()->make(TokenService::class);

        if (!$tokenService->getUserIdByToken($this->get('token'))) {
            $validator->errors()->add('token', __('Invalid token'));
        }
    }
}
