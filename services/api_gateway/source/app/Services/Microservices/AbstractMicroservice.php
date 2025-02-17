<?php

declare(strict_types=1);

namespace App\Services\Microservices;

use App\Http\Requests\ApiRequest;
use App\Services\RabbitMQService;

abstract class AbstractMicroservice
{
    protected RabbitMQService $rabbitMQService;

    public function __construct(protected string $queue)
    {
        $this->rabbitMQService = app()->make(RabbitMQService::class);
    }

    public function doRequest(ApiRequest $request, string $action): ?array
    {
        return $this->rabbitMQService->sendRpcRequest($this->queue, $action, $request->toArray());
    }
}
