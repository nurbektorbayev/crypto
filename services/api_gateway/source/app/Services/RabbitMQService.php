<?php

declare(strict_types=1);

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(string $host, int $port, string $username, string $password)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $username, $password);
        $this->channel = $this->connection->channel();
    }

    /**
     * 📌 Отправка RPC-запроса (API Gateway → Users)
     */
    public function sendRpcRequest(string $queue, string $action, array $data, int $timeout = 5): ?array
    {
        // Объявляем очередь (гарантируем, что она существует)
        $this->channel->queue_declare($queue, false, true, false, false);

        // Создаем временную очередь для ответа
        $replyQueue = $queue . '.reply';
        $this->channel->queue_declare($replyQueue, false, false, false, false);

        $correlationId = uniqid();

        $message = [
            'action' => $action,
            'data' => $data
        ];

        // Создаем сообщение
        $msg = new AMQPMessage(json_encode($message), [
            'correlation_id' => $correlationId,
            'reply_to' => $replyQueue,
            'delivery_mode'  => 2, // 2 = Персистентное сообщение
        ]);

        // Отправляем сообщение в очередь RabbitMQ
        $this->channel->basic_publish($msg, '', $queue);

        // Ждем ответ
        $response = null;
        $startTime = time();

        $callback = function ($msg) use (&$response, $correlationId) {
            if ($msg->get('correlation_id') === $correlationId) {
                $response = json_decode($msg->getBody(), true);
            }
        };

        $this->channel->basic_consume($replyQueue, '', false, true, false, false, $callback);

//        while (!$response) {
//            $this->channel->wait();
//        }
//
//        return $response;

        while ($response === null) {
            $this->channel->wait(null, false, 1); // Ждем 1 секунду

            if ((time() - $startTime) > $timeout) {
                throw new \Exception('Microservice has not responded');
            }
        }

        return $response;
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
