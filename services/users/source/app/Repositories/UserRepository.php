<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\User\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function registerWithTelegram(array $data, ?User $user): User
    {
        if (!$user) {
            $user = new User();
            $user->status = UserStatus::Active;
        }

        $user->name = $data['name'];
        $user->telegram_id = $data['telegram_id'];

        $user->save();

        return $user;
    }

    public function registerWithEmail(array $data, ?User $user): User
    {
        if (!$user) {
            $user = new User();
            $user->status = UserStatus::Active;
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);

        $user->save();

        return $user;
    }

    public function findOneByEmail(string $email): ?User
    {
        /** @var User|null $model */
        $model = User::query()
            ->where('email', $email)
            ->where('status', UserStatus::Active)
            ->first();

        return $model;
    }

    public function findOneByTelegramId(string $telegramId): ?User
    {
        /** @var User|null $model */
        $model = User::query()
            ->where('telegram_id', $telegramId)
            ->where('status', UserStatus::Active)
            ->first();

        return $model;
    }

    public function findOneById(int $id): ?User
    {
        /** @var User|null $model */
        $model = User::query()
            ->where('id', $id)
            ->where('status', UserStatus::Active)
            ->first();

        return $model;
    }
}
