<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RabbitMQService;
use App\Transport\Middlewares\RabbitMQRequestMiddleware;
use App\Transport\Handlers\RabbitMQRequestHandler;

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

        $this->rabbitMQService->consume(function ($message) {
            $this->middleware->handle($message, function ($msg, $request) {
                $this->requestHandler->handleRequest($msg, $request);
            });
        });
    }
}
