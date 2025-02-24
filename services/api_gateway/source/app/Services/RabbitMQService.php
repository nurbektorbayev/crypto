<?php

declare(strict_types=1);

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use App\Exceptions\MicroserviceException;

class RabbitMQService
{
    private ?AMQPStreamConnection $connection = null;
    private ?AMQPChannel $channel = null;
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private int $heartbeatInterval = 30; // Heartbeat every 30 seconds
    private bool $keepAlive = true; // Enable TCP keepalive

    public function __construct(string $host, int $port, string $username, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * 📌 Инициализация соединения (Lazy Initialization)
     */
    private function connect(): void
    {
        if (!$this->connection || !$this->channel) {
            // Создаем соединение с поддержкой keepalive и heartbeat
            $this->connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->username,
                $this->password,
                '/',
                false,   // insist
                'AMQPLAIN',   // login_method
                null,   // login_response
                'en_US',   // locale
                5.0,   // timeout на установку соединения
                5.0,   // timeout на чтение
                null,   // write_timeout
                $this->keepAlive, // Включаем TCP Keepalive
                $this->heartbeatInterval // Периодический heartbeat
            );
            $this->channel = $this->connection->channel();
        }
    }

    /**
     * 📌 Отправка RPC-запроса (API Gateway → Users)
     */
    public function sendRpcRequest(string $queue, string $action, array $payload, int $timeout = 3): MicroserviceResponse
    {
        $this->connect();

        // Объявляем очередь (гарантируем, что она существует)
        $this->channel->queue_declare($queue, false, true, false, false);

        // Создаем временную очередь для ответа
        $replyQueue = $queue . '.reply';
        $this->channel->queue_declare($replyQueue, false, false, false, false);

        $correlationId = uniqid();
        $message = json_encode(['action' => $action, 'payload' => $payload]);

        // Создаем сообщение
        $msg = new AMQPMessage($message, [
            'correlation_id' => $correlationId,
            'reply_to' => $replyQueue,
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT, // 2 = Персистентное сообщение
        ]);

        try {
            // Отправляем сообщение
            $this->channel->basic_publish($msg, '', $queue);
        } catch (\Exception $e) {
            throw new MicroserviceException('Failed to publish message to RabbitMQ', 500, ['error' => $e->getMessage()]);
        }

        return $this->waitForResponse($replyQueue, $correlationId, $timeout);
    }

    /**
     * 📌 Ожидание ответа от сервиса
     */
    private function waitForResponse(string $replyQueue, string $correlationId, int $timeout): MicroserviceResponse
    {
        $response = null;
        $startTime = time();

        $callback = function (AMQPMessage $msg) use (&$response, $correlationId) {
            if ($msg->get('correlation_id') === $correlationId) {
                $response = json_decode($msg->getBody(), true);
            }
        };

        $this->channel->basic_consume($replyQueue, '', false, true, false, false, $callback);

        while ($response === null && (time() - $startTime) < $timeout) {
            try {
                $this->channel->wait(null, false, 1); // Ждем 1 секунду
            } catch (\Exception $e) {
                throw new MicroserviceException('Error while waiting for response', 500, ['error' => $e->getMessage()]);
            }
        }

        if ($response === null) {
            throw new MicroserviceException('Microservice did not respond within timeout', 504);
        }

        $microserviceResponse = new MicroserviceResponse($response);

        if ($exception = $microserviceResponse->buildException()) {
            throw $exception;
        }

        return $microserviceResponse;
    }

    /**
     * 📌 Закрываем соединение
     */
    public function close(): void
    {
        if ($this->channel) {
            $this->channel->close();
            $this->channel = null;
        }
        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
    }
}
