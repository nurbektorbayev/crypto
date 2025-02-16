<?php

declare(strict_types=1);

namespace App\Services\Microservices;

use App\Transport\Requests\RabbitMQRequest;

abstract class AbstractMicroservice
{
    private RabbitMQRequest $rabbitMQRequest;

    public function __construct(private array $config)
    {
        $this->rabbitMQRequest = app()->make(RabbitMQRequest::class);
    }

    public function doRequest()
    {
        $this->rabbitMQRequest->doRequest();
    }
}
