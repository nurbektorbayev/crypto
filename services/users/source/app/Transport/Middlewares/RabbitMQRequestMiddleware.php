<?php

declare(strict_types=1);

namespace App\Transport\Middlewares;

use App\Repositories\UserRepository;
use App\Transport\Message\RabbitMQMessagePayload;
use App\Transport\Message\RabbitMQMessageRequest;
use App\Transport\Message\RabbitMQMessageResponse;
use App\Transport\Requests\RabbitMQRequest;
use Illuminate\Support\Facades\Validator;
use PhpAmqpLib\Message\AMQPMessage;
use App\Services\RabbitMQService;

class RabbitMQRequestMiddleware
{
    public function __construct(private RabbitMQService $rabbitMQService, private UserRepository $userRepository)
    {
    }

    public function handle(AMQPMessage $message, callable $next)
    {
        try {
            $messageRequest = new RabbitMQMessageRequest($message);

            $action = $messageRequest->getAction();

            // Определяем Request-класс по `action`
            $requestClass = $action->getRequestClass();
            if (!$requestClass) {
                throw new \Exception("Invalid action: " . $action->value);
            }

            // Создаем Laravel Request
            /** @var RabbitMQRequest $request */
            $request = new $requestClass($messageRequest->getPayload()->toArray());

            // Загружаем пользователя, если передан user_id
            $userId = $messageRequest->getUserId();
            $user = is_int($userId) && $userId ? $this->userRepository->findOneById($userId) : null;
            $request->setUserResolver(fn() => $user);

            // Запускаем валидацию
            $validator = Validator::make($request->all(), $request->rules());

            if ($validator->fails()) {
                $messageResponse = new RabbitMQMessageResponse($messageRequest->getCorrelationId(), new RabbitMQMessagePayload($validator));
                $this->rabbitMQService->sendResponse($messageResponse, $messageRequest->getReplyTo());
                return null;
            }

//            if ($validator->fails()) {
//                $messageResponse = new RabbitMQMessageResponse($messageRequest->getCorrelationId(), new RabbitMQMessagePayload($validator));
//                $this->rabbitMQService->sendResponse($messageResponse, $messageRequest->getReplyTo());
//                return null;
//            }

            // Передаем управление в основной обработчик
            return $next($messageRequest, $request);
        } catch (\Throwable $e) {
            $messageResponse = new RabbitMQMessageResponse($message->get('correlation_id'), new RabbitMQMessagePayload($e));

            $this->rabbitMQService->sendResponse($messageResponse, $message->get('reply_to'));

            return null;
        }
    }
}
