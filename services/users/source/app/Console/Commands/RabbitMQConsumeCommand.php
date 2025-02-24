<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Transport\Message\RabbitMQMessage;
use App\Transport\Message\RabbitMQMessageRequest;
use App\Transport\Requests\RabbitMQRequest;
use Illuminate\Console\Command;
use App\Services\RabbitMQService;
use App\Transport\Middlewares\RabbitMQRequestMiddleware;
use App\Transport\Handlers\RabbitMQRequestHandler;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumeCommand extends Command
{
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Consume messages from RabbitMQ';

    public function __construct(
        private RabbitMQService $rabbitMQService,
        private RabbitMQRequestMiddleware $middleware,
        private RabbitMQRequestHandler $requestHandler
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info("Listening for RabbitMQ messages...");

        $this->rabbitMQService->consume(function (AMQPMessage $originalMessage) {
            $this->middleware->handle($originalMessage, function (RabbitMQMessageRequest $message, RabbitMQRequest $request) {
                $this->requestHandler->handleRequest($message, $request);
            });
        });
    }
}
