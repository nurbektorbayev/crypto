<?php

declare(strict_types=1);

namespace App\Gateways;

use App\Transport\Requests\Auth\LoginWithEmailRequest;
use App\Transport\Requests\Auth\LoginWithTelegramRequest;
use App\Transport\Requests\Auth\RegisterWithEmailRequest;
use App\Transport\Requests\Auth\RegisterWithTelegramRequest;
use App\Transport\Requests\Auth\ValidateTokenRequest;
use App\Transport\Responses\TransformsResponses;
use App\Transport\Transformers\User\UserTransformer;
use App\Repositories\UserRepository;

class AuthGateway
{
    use TransformsResponses;

    public function __construct(private UserRepository $userRepository)
    {
        $this->setModelTransformer(new UserTransformer());
    }

    public function registerWithTelegram(RegisterWithTelegramRequest $request): array
    {
        $user = $this->userRepository->registerWithTelegram($request->all(), $request->user());

        return $this->convertModelJsonData($request, $user, ['token']);
    }

    public function registerWithEmail(RegisterWithEmailRequest $request): array
    {
        $user = $this->userRepository->registerWithEmail($request->all(), $request->user());

        return $this->convertModelJsonData($request, $user, ['token']);
    }

    public function loginWithTelegram(LoginWithTelegramRequest $request): array
    {
        $user = $this->userRepository->findOneByTelegramId($request->get('telegram_id'));

        return $this->convertModelJsonData($request, $user, ['token']);
    }

    public function loginWithEmail(LoginWithEmailRequest $request): array
    {
        $user = $this->userRepository->findOneByEmail($request->get('email'));

        return $this->convertModelJsonData($request, $user, ['token']);
    }

    // Метод для валидации токена
    public function validateToken(ValidateTokenRequest $request): array
    {
        return [];
    }
}
