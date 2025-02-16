<?php

declare(strict_types=1);

namespace App\Transport\Middlewares;

use App\Enums\RabbitMQAction;
use App\Repositories\UserRepository;
use App\Transport\Responses\FormattedJSONResponse;
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
        $data = json_decode($message->getBody(), true);
        $correlationId = $message->get('correlation_id');
        $replyTo = $message->get('reply_to');

        try {
            $action = $this->getRabbitMQAction($data['action'] ?? null);

            // Определяем Request-класс по `action`
            $requestClass = $action->getRequestClass();
            if (!$requestClass) {
                throw new \Exception("Invalid action: " . $data['action']);
            }

            // Создаем Laravel Request
            $request = new $requestClass($data['data'] ?? []);

            // Загружаем пользователя, если передан user_id
            $userId = $data['user_id'] ?? null;
            $user = is_int($userId) && $userId ? $this->userRepository->findOneById($userId) : null;
            $request->setUserResolver(fn() => $user);

            // Запускаем валидацию
            $validator = Validator::make($request->all(), $request->rules());

            if ($validator->fails()) {
                $this->sendError($replyTo, $correlationId, 422, $validator->getMessageBag()->first(), $validator->errors()->toArray());
                return null;
            }

            // Передаем управление в основной обработчик
            return $next($message, $request);
        } catch (\Exception $e) {
            $this->sendError($replyTo, $correlationId, $e->getCode(), $e->getMessage());
            return null;
        }
    }

    private function sendError($replyTo, $correlationId, int $code, string $message, array $errors = []): void
    {
        $this->rabbitMQService->sendResponse($correlationId, $replyTo, FormattedJSONResponse::error($code, $message, $errors));
    }

    private function getRabbitMQAction(?string $action): RabbitMQAction
    {
        if ($action === null) {
            throw new \Exception("Missing 'action' field in request.");
        }
        $actionEnum = RabbitMQAction::tryFrom($action);

        if (!$actionEnum) {
            throw new \Exception("Invalid action: " . $action);

        }

        return $actionEnum;
    }
}
