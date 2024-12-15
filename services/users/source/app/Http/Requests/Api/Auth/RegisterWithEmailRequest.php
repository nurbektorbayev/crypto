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
 *     schema="RegisterWithEmailRequest",
 *     description="Request model for user registering via email",
 *     required={"name", "email", "password", "password_confirmation"},
 *     @OA\Property(property="name", type="string", description="Name of registering user", example="John Doe"),
 *     @OA\Property(property="email", type="string", description="Email of registering user", example="john.doe@gmail.com"),
 *     @OA\Property(property="password", type="string", description="Password", example="123456"),
 *     @OA\Property(property="password_confirmation", type="string", description="Password confirmation", example="123456"),
 * )
 *
 * @OA\RequestBody(
 *     request="RegisterWithEmailRequest",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/RegisterWithEmailRequest"),
 * )
 */
class RegisterWithEmailRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
