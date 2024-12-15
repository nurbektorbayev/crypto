<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiRequest;
use OpenApi\Annotations as OA;

/**
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $password_confirmation
 *
 * @OA\Schema(
 *     schema="RegisterWithTelegramRequest",
 *     description="Request model for user registering via Telegram",
 *     required={"name", "telegram_id"},
 *     @OA\Property(property="name", type="string", description="Name of registering user", example="John Doe"),
 *     @OA\Property(property="telegram_id", type="string", description="Telegram ID of registering user", example="12332131232"),
 * )
 *
 * @OA\RequestBody(
 *     request="RegisterWithTelegramRequest",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/RegisterWithTelegramRequest"),
 * )
 */
class RegisterWithTelegramRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2',
            'telegram_id' => 'required|string',  // ID Telegram пользователя
        ];
    }
}
