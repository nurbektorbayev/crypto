<?php

declare(strict_types=1);

namespace App\Transport\Handlers;

use App\Services\RabbitMQService;
use App\Transport\Message\RabbitMQMessagePayload;
use App\Transport\Message\RabbitMQMessageRequest;
use App\Transport\Message\RabbitMQMessageResponse;
use Illuminate\Http\Request;

class RabbitMQRequestHandler
{
    public function __construct(private RabbitMQService $rabbitMQService)
    {
    }

    public function handleRequest(RabbitMQMessageRequest $message, Request $request): void
    {
        try {
            // Получаем action из запроса
            $action = $message->getAction();

            // Получаем Gateway и метод
            [$gatewayClass, $method] = $action->getMethod();

            // Создаем экземпляр Gateway через Laravel container
            $gatewayInstance = app()->make($gatewayClass);

            if (!method_exists($gatewayInstance, $method)) {
                throw new \Exception("Method '$method' not found in " . $gatewayClass);
            }

            // Вызываем метод Gateway с передачей Request
            $response = $gatewayInstance->$method($request);

            if (!is_array($response)) {
                throw new \Exception("Response is not an array");
            }

            $responseMessage = new RabbitMQMessageResponse($message->getCorrelationId(), new RabbitMQMessagePayload($response));

            // Отправляем ответ обратно в API Gateway через RabbitMQ
            $this->rabbitMQService->sendResponse($responseMessage, $message->getReplyTo());

        } catch (\Exception $e) {
            $responseMessage = new RabbitMQMessageResponse($message->getCorrelationId(), new RabbitMQMessagePayload($e));

            $this->rabbitMQService->sendResponse($responseMessage, $message->getReplyTo());

            throw $e;
        }
    }
}
