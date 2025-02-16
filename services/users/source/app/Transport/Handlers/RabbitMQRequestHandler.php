<?php

declare(strict_types=1);

namespace App\Transport\Handlers;

use App\Enums\RabbitMQAction;
use App\Services\RabbitMQService;
use App\Transport\Responses\FormattedJSONResponse;
use Illuminate\Http\Request;

class RabbitMQRequestHandler
{
    public function __construct(private RabbitMQService $rabbitMQService)
    {
    }

    public function handleRequest($message, Request $request): void
    {
        $correlationId = $message->get('correlation_id');
        $replyTo = $message->get('reply_to');
        $data = json_decode($message->getBody(), true);

        try {
            // Получаем action из запроса
            $action = $this->getRabbitMQAction($data['action'] ?? null);

            // Получаем Gateway и метод
            [$gatewayClass, $method] = $action->getMethod();

            // Создаем экземпляр Gateway через Laravel container
            $gatewayInstance = app($gatewayClass);

            if (!method_exists($gatewayInstance, $method)) {
                throw new \Exception("Method '$method' not found in " . $gatewayClass);
            }

            // Вызываем метод Gateway с передачей Request
            $response = $gatewayInstance->$method($request);

            // Отправляем ответ обратно в API Gateway через RabbitMQ
            $this->rabbitMQService->sendResponse($correlationId, $replyTo, $response);

        } catch (\Exception $e) {
            $this->rabbitMQService->sendResponse($correlationId, $replyTo, FormattedJSONResponse::error($e->getCode(), $e->getMessage()));

            throw $e;
        }
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
