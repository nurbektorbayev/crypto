<?php

declare(strict_types=1);

namespace App\Gateways;

use App\Transport\Requests\Auth\LoginWithEmailRequest;
use App\Transport\Requests\Auth\LoginWithTelegramRequest;
use App\Transport\Requests\Auth\RegisterWithEmailRequest;
use App\Transport\Requests\Auth\RegisterWithTelegramRequest;
use App\Transport\Responses\FormattedJSONResponse;
use App\Transport\Responses\TransformsResponses;
use App\Transport\Transformers\User\UserTransformer;
use App\Repositories\UserRepository;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;

class AuthGateway
{
    use TransformsResponses;

    public function __construct(private TelegramService $telegramService, private UserRepository $userRepository)
    {
        $this->setModelTransformer(new UserTransformer());
    }

    public function registerWithTelegram(RegisterWithTelegramRequest $request): JsonResponse
    {
        $user = $this->userRepository->registerWithTelegram($request->validated(), $request->user());

        return FormattedJSONResponse::created($this->convertModelJsonData($request, $user, ['token']), 'User has been created');
    }

    public function registerWithEmail(RegisterWithEmailRequest $request): JsonResponse
    {
        $user = $this->userRepository->registerWithEmail($request->validated(), $request->user());

        return FormattedJSONResponse::created($this->convertModelJsonData($request, $user, ['token']), 'User has been created');
    }

    public function loginWithTelegram(LoginWithTelegramRequest $request): JsonResponse
    {
        $user = $this->userRepository->findOneByTelegramId($request->validated('telegram_id'));

        return FormattedJSONResponse::show($this->convertModelJsonData($request, $user, ['token']), 'User logged in successfully');
    }

    public function loginWithEmail(LoginWithEmailRequest $request): JsonResponse
    {
        $user = $this->userRepository->findOneByEmail($request->validated('email'));

        return FormattedJSONResponse::show($this->convertModelJsonData($request, $user, ['token']), 'User logged in successfully');
    }
}
