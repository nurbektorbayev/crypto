<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use OpenApi\Annotations as OA;

/**
 * @property string $telegram_id
 *
 * @OA\Schema(
 *     schema="LoginWithTelegramRequest",
 *     description="Request model for user login via telegram",
 *     required={"telegram_id"},
 *     @OA\Property(property="telegram_id", type="string", description="Telegram ID of user", example="31231232312313"),
 * )
 *
 * @OA\RequestBody(
 *     request="LoginWithTelegramRequest",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/LoginWithTelegramRequest"),
 * )
 */
class LoginWithTelegramRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
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
