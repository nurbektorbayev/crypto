<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Microservices\UsersMicroservice;
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

            if (empty($host)) {
                throw new \Exception('RabbitMQ error: check RABBITMQ_HOST in .env');
            }

            if (empty($port)) {
                throw new \Exception('RabbitMQ error: check RABBITMQ_PORT in .env');
            }

            if (empty($username)) {
                throw new \Exception('RabbitMQ error: check RABBITMQ_USERNAME in .env');
            }

            if (empty($password)) {
                throw new \Exception('RabbitMQ error: check RABBITMQ_PASSWORD in .env');
            }

            return new RabbitMQService($host, $port, $username, $password);
        });

        $this->app->singleton(UsersMicroservice::class, function () {
            $queue = config('microservices.users.queue');

            if (empty($queue)) {
                throw new \Exception('Users microservice error: check RABBITMQ_QUEUE_USERS in .env');
            }

            return new UsersMicroservice($queue);
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
