<?php

declare(strict_types=1);

namespace App\Enums\User;

enum UserStatus: int
{
    case WaitingEmailVerification = 0;

    case Banned = 10;

    case Active = 100;
}
