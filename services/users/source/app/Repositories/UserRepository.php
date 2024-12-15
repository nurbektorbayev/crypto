<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\User\UserStatus;
use App\Http\Requests\Api\Auth\RegisterWithEmailRequest;
use App\Http\Requests\Api\Auth\RegisterWithTelegramRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function registerWithTelegram(RegisterWithTelegramRequest $request): User
    {
        $validated = $request->validated();
        $model = $request->user();

        if (!$model) {
            $model = new User();
            $model->status = UserStatus::Active;
        }

        $model->name = $validated['name'];
        $model->telegram_id = $validated['telegram_id'];

        $model->save();

        return $model;
    }

    public function registerWithEmail(RegisterWithEmailRequest $request): User
    {
        $validated = $request->validated();
        $model = $request->user();

        if (!$model) {
            $model = new User();
            $model->status = UserStatus::Active;
        }

        $model->name = $validated['name'];
        $model->email = $validated['email'];
        $model->password = Hash::make($validated['password']);

        $model->save();

        return $model;
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
}
