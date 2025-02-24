<?php

declare(strict_types=1);

namespace App\Gateways;

use App\Repositories\UserRepository;
use App\Transport\Requests\User\GetUserRequest;
use App\Transport\Responses\TransformsResponses;
use App\Transport\Transformers\User\UserTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserGateway
{
    use TransformsResponses;

    public function __construct(private UserRepository $userRepository)
    {
        $this->setModelTransformer(new UserTransformer());
    }

    public function getUserByRequest(GetUserRequest $request): array
    {
        $model = $this->userRepository->findOneById($request->get('id'));

        if (!$model) {
            throw new ModelNotFoundException('User not found');
        }

        return $this->convertModelJsonData($request, $model);
    }
}
