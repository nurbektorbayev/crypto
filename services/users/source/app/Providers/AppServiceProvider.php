<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\RabbitMQService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RabbitMQService::class, function () {
            $host = config('services.rabbitmq.host');
            $port = config('services.rabbitmq.port');
            $username = config('services.rabbitmq.username');
            $password = config('services.rabbitmq.password');
            $queue = config('services.rabbitmq.queue');

            if (empty($host)) {
                throw new \Exception('RabbitMQ error: check HOST in .env');
            }

            if (empty($port)) {
                throw new \Exception('RabbitMQ error: check PORT in .env');
            }

            if (empty($username)) {
                throw new \Exception('RabbitMQ error: check USERNAME in .env');
            }

            if (empty($password)) {
                throw new \Exception('RabbitMQ error: check PASSWORD in .env');
            }

            if (empty($queue)) {
                throw new \Exception('RabbitMQ error: check QUEUE in .env');
            }

            return new RabbitMQService($host, $port, $username, $password, $queue);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
