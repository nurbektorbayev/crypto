<?php

declare(strict_types=1);

namespace App\Services\Microservices;

use App\Http\Requests\Auth\LoginWithEmailRequest;

class UsersMicroservice extends AbstractMicroservice
{
    public function loginWithEmail(LoginWithEmailRequest $request): ?array
    {
        return $this->doRequest($request, 'login_with_email');
    }
}
