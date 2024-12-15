<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Auth\LoginWithEmailRequest;
use App\Http\Requests\Api\Auth\LoginWithTelegramRequest;
use App\Http\Requests\Api\Auth\RegisterWithEmailRequest;
use App\Http\Requests\Api\Auth\RegisterWithTelegramRequest;
use App\Http\Responses\FormattedJSONResponse;
use App\Http\Transformers\User\UserWithTokenTransformer;
use App\Repositories\UserRepository;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class AuthController extends ApiController
{
    public function __construct(private TelegramService $telegramService, private UserRepository $userRepository)
    {
        $this->setModelTransformer(new UserWithTokenTransformer());
    }

    /**
     * @OA\Post(
     *      path="/api/auth/register-via-telegram",
     *      tags={"auth"},
     *      summary="Creates new user",
     *      description="Creates new user",
     *      requestBody={"$ref": "#/components/requestBodies/RegisterWithTelegramRequest"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(allOf={
     *                @OA\Schema(ref="#/components/schemas/StandardDataResponse"),
     *                @OA\Schema(@OA\Property(property="data", ref="#/components/schemas/UserWithTokenResource")),
     *              })
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Validation error"),
     *  )
     */
    public function registerWithTelegram(RegisterWithTelegramRequest $request): JsonResponse
    {
        $user = $this->userRepository->registerWithTelegram($request);

        return FormattedJSONResponse::created($this->convertModelJsonData($request, $user), 'User has been created');

    }

    /**
     * @OA\Post(
     *      path="/api/auth/register-via-email",
     *      tags={"auth"},
     *      summary="Creates new user",
     *      description="Creates new user",
     *      requestBody={"$ref": "#/components/requestBodies/RegisterWithEmailRequest"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(allOf={
     *                @OA\Schema(ref="#/components/schemas/StandardDataResponse"),
     *                @OA\Schema(@OA\Property(property="data", ref="#/components/schemas/UserWithTokenResource")),
     *              })
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Validation error"),
     *  )
     */
    public function registerWithEmail(RegisterWithEmailRequest $request): JsonResponse
    {
        $user = $this->userRepository->registerWithEmail($request);

        return FormattedJSONResponse::created($this->convertModelJsonData($request, $user), 'User has been created');
    }

    /**
     * @OA\Post(
     *      path="/api/auth/login-via-telegram",
     *      tags={"auth"},
     *      summary="Login for user via telegram",
     *      description="Login for user via telegram",
     *      requestBody={"$ref": "#/components/requestBodies/LoginWithTelegramRequest"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(allOf={
     *                @OA\Schema(ref="#/components/schemas/StandardDataResponse"),
     *                @OA\Schema(@OA\Property(property="data", ref="#/components/schemas/UserWithTokenResource")),
     *              })
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Validation error"),
     *  )
     */
    public function loginWithTelegram(LoginWithTelegramRequest $request): JsonResponse
    {
        $user = $this->userRepository->findOneByTelegramId($request->telegram_id);

        return FormattedJSONResponse::show($this->convertModelJsonData($request, $user), 'User logged in successfully');
    }

    /**
     * @OA\Post(
     *      path="/api/auth/login-via-email",
     *      tags={"auth"},
     *      summary="Login for user via email",
     *      description="Login for user via email",
     *      requestBody={"$ref": "#/components/requestBodies/LoginWithEmailRequest"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(allOf={
     *                @OA\Schema(ref="#/components/schemas/StandardDataResponse"),
     *                @OA\Schema(@OA\Property(property="data", ref="#/components/schemas/UserWithTokenResource")),
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
        $user = $this->userRepository->findOneByEmail($request->email);

        return FormattedJSONResponse::show($this->convertModelJsonData($request, $user), 'User logged in successfully');
    }
}
