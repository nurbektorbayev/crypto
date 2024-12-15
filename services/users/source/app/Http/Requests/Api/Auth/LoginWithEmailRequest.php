<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use OpenApi\Annotations as OA;

/**
 * @property string $email
 * @property string $password
 *
 * @OA\Schema(
 *     schema="LoginWithEmailRequest",
 *     description="Request model for user login via email",
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

    public function withValidator(Validator $validator): void
    {
        $validator->after([$this, 'validateUserExists']);
    }

    public function validateUserExists(Validator $validator): void
    {
        if (!Auth::attempt($this->only(['email', 'password']))) {
            $validator->errors()->add('email', __('Invalid email or password'));
            $validator->errors()->add('password', __('Invalid email or password'));
        }
    }
}
