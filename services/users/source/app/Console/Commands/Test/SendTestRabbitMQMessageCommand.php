<?php

declare(strict_types=1);

namespace App\Console\Commands\Test;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;

class SendTestRabbitMQMessageCommand extends Command
{
    protected $signature = 'test:send-test-rabbitmq-message {action=get_user} {user_id=1}';
    protected $description = 'Send a test message to RabbitMQ for users_microservice';

    public function handle()
    {
        $queueName      = config('services.rabbitmq.queue'); // Основная очередь
        $replyQueueName = $queueName . '.reply'; // Очередь для ответов
        $rabbitHost     = config('services.rabbitmq.host');
        $rabbitPort     = config('services.rabbitmq.port');
        $rabbitUser     = config('services.rabbitmq.username');
        $rabbitPass     = config('services.rabbitmq.password');

        $action = $this->argument('action');
        $userId = (int) $this->argument('user_id');

        try {
            // Подключение к RabbitMQ
            $connection = new AMQPStreamConnection($rabbitHost, $rabbitPort, $rabbitUser, $rabbitPass);
            $channel = $connection->channel();

            // Декларируем основную очередь (если её нет)
            $channel->queue_declare($queueName, false, true, false, false);

            // Декларируем очередь для ответов (если её нет)
            $channel->queue_declare($replyQueueName, false, false, false, false);

            // Генерируем `correlation_id`
            $correlationId = Uuid::uuid4()->toString();

            // Формируем тестовое сообщение
            $data = [
                'action'  => $action,
                'user_id' => $userId,
                'data'    => ['id' => $userId]
            ];

            $msg = new AMQPMessage(
                json_encode($data),
                [
                    'delivery_mode'  => 2, // 2 = Персистентное сообщение
                    'correlation_id' => $correlationId,
                    'reply_to'       => $replyQueueName // Теперь консюмер отправит ответ сюда
                ]
            );

            // Отправляем в основную очередь
            $channel->basic_publish($msg, '', $queueName);

            $this->info("✅ [Sent] Action: '$action' for user_id: $userId (correlation_id: $correlationId)");

            $channel->close();
            $connection->close();
        } catch (\Exception $e) {
            $this->error("❌ Ошибка при отправке сообщения: " . $e->getMessage());
        }
    }
}
