<?php

declare(strict_types=1);

namespace App\Transport\Message;

use App\Enums\RabbitMQAction;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQMessageRequest extends RabbitMQMessage
{
    private RabbitMQAction $action;

    private string $replyTo;

    private ?int $userId = null;

    public function __construct(AMQPMessage $originalMessage)
    {
        $body = json_decode($originalMessage->getBody(), true);

        parent::__construct($originalMessage->get('correlation_id'), new RabbitMQMessagePayload($body['payload'] ?? []));

        $this->action = $this->getRabbitMQAction($body['action'] ?? null);
        $this->replyTo = $originalMessage->get('reply_to');
        $this->userId = isset($body['user_id']) && is_int($body['user_id']) ? $body['user_id'] : null;
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

    public function getAction(): RabbitMQAction
    {
        return $this->action;
    }

    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
