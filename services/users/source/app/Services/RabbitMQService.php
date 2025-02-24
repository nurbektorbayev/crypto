<?php

declare(strict_types=1);

namespace App\Services;

use App\Transport\Message\RabbitMQMessageResponse;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQService
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(string $host, int $port, string $username, string $password, private string $queue)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $username, $password);
        $this->channel = $this->connection->channel();

        // Объявляем очередь (гарантируем, что она существует)
        $this->channel->queue_declare($this->queue, false, true, false, false);
    }

    /**
     * 📌 Отправка ответа от Users обратно в API Gateway
     */
    public function sendResponse(RabbitMQMessageResponse $messageResponse, string $replyTo): void
    {
        $originalMessage = $messageResponse->toOriginalMessage();

        $this->channel->basic_publish($originalMessage, '', $replyTo);
    }

    /**
     * 📌 Слушаем очередь RabbitMQ и обрабатываем сообщения
     */
    public function consume(callable $callback): void
    {
        $this->channel->basic_consume($this->queue, '', false, true, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    /**
     * 📌 Закрываем соединение
     */
    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
