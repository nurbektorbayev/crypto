<?php

declare(strict_types=1);

namespace App\Enums;

use App\Gateways\AuthGateway;
use App\Gateways\UserGateway;
use App\Transport\Requests\Auth\LoginWithEmailRequest;
use App\Transport\Requests\Auth\LoginWithTelegramRequest;
use App\Transport\Requests\Auth\RegisterWithEmailRequest;
use App\Transport\Requests\Auth\RegisterWithTelegramRequest;
use App\Transport\Requests\User\GetUserRequest;

enum RabbitMQAction: string
{
    use ValuesTrait;

    /**
     * User
     */
    case GET_USER = 'get_user';

    /**
     * AUTH
     */
    case LOGIN_WITH_EMAIL = 'login_with_email';
    case LOGIN_WITH_TELEGRAM = 'login_with_telegram';
    case REGISTER_WITH_EMAIL = 'register_with_email';
    case REGISTER_WITH_TELEGRAM = 'register_with_telegram';

    public function getRequestClass(): string
    {
        return match ($this) {
            self::GET_USER => GetUserRequest::class,
            self::LOGIN_WITH_EMAIL => LoginWithEmailRequest::class,
            self::LOGIN_WITH_TELEGRAM => LoginWithTelegramRequest::class,
            self::REGISTER_WITH_EMAIL => RegisterWithEmailRequest::class,
            self::REGISTER_WITH_TELEGRAM => RegisterWithTelegramRequest::class,
        };
    }

    public function getMethod(): array
    {
        return match ($this) {
            self::GET_USER => [UserGateway::class, 'getUserByRequest'],
            self::LOGIN_WITH_EMAIL => [AuthGateway::class, 'loginWithEmail'],
            self::LOGIN_WITH_TELEGRAM => [AuthGateway::class, 'loginWithTelegram'],
            self::REGISTER_WITH_EMAIL => [AuthGateway::class, 'registerWithEmail'],
            self::REGISTER_WITH_TELEGRAM => [AuthGateway::class, 'registerWithTelegram'],
        };
    }
}
