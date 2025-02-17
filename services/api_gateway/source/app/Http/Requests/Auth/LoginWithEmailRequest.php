<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiRequest;
use OpenApi\Annotations as OA;

/**
 * @property string $email
 * @property string $password
 *
 * @OA\Schema(
 *     schema="LoginWithEmailRequest",
 *     description="Request model for user login",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", description="Email of user", example="john.doe@gmail.com"),
 *     @OA\Property(property="password", type="string", description="Password", format="date", example="123456"),
 * )
 *
 * @OA\RequestBody(
 *     request="LoginWithEmailRequest",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/LoginWithEmailRequest"),
 * )
 */
class LoginWithEmailRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required'
        ];
    }
}
