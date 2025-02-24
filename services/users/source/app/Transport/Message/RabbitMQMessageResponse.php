<?php

declare(strict_types=1);

namespace App\Transport\Message;

use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQMessageResponse extends RabbitMQMessage
{
    public function toOriginalMessage(): AMQPMessage
    {
        $body = [
            'payload' => $this->getPayload()->toArray(),
        ];

        return new AMQPMessage(json_encode($body), [
            'correlation_id' => $this->getCorrelationId(),
        ]);
    }
}
