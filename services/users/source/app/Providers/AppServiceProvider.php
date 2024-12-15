<?php

namespace App\Providers;

use App\Services\TelegramService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TelegramService::class, function () {
            $apiUrl = config('services.telegram.api_url');
            $botToken = config('services.telegram.bot_token');

            if (empty($apiUrl)) {
                throw new \Exception('Telegram error: check API URL in .env');
            }

            if (empty($botToken)) {
                throw new \Exception('Telegram error: check bot token in .env');
            }

            return new TelegramService($apiUrl . '/bot' . $botToken . '/');
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
