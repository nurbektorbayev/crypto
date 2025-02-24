<?php

declare(strict_types=1);

namespace App\Services\Microservices;

use App\Http\Requests\Auth\LoginWithEmailRequest;
use App\Services\MicroserviceResponse;

class UsersMicroservice extends AbstractMicroservice
{
    public function loginWithEmail(LoginWithEmailRequest $request): MicroserviceResponse
    {
        return $this->doRequest($request, 'login_with_email');
    }
}
