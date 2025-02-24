<?php

declare(strict_types=1);

namespace App\Transport\Message;

abstract class RabbitMQMessage
{
    public function __construct(private string $correlationId, private RabbitMQMessagePayload $payload)
    {
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    public function getPayload(): RabbitMQMessagePayload
    {
        return $this->payload;
    }
}
