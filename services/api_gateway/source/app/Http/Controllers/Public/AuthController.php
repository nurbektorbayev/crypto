<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\LoginWithEmailRequest;
use App\Http\Responses\FormattedJSONResponse;
use App\Services\Microservices\UsersMicroservice;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class AuthController extends ApiController
{
    public function __construct(private readonly UsersMicroservice $usersMicroservice)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/auth/login-with-email",
     *      operationId="loginWithEmail",
     *      tags={"auth"},
     *      summary="Login with email for user",
     *      description="Login with email for user",
     *      requestBody={"$ref": "#/components/requestBodies/LoginWithEmailRequest"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(allOf={
     *                @OA\Schema(ref="#/components/schemas/StandardDataResponse"),
     *              })
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Validation error"),
     *  )
     */
    public function loginWithEmail(LoginWithEmailRequest $request): JsonResponse
    {
        $response = $this->usersMicroservice->loginWithEmail($request);

        return FormattedJSONResponse::show($response->getPayload());
    }
}
