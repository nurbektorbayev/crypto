<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RestartSupervisorConsumerCommand extends Command
{
    protected $signature = 'supervisor:restart-consumer';
    protected $description = 'Restart the RabbitMQ consumer in Supervisor';

    public function handle(): void
    {
        $this->info('🔄 Перезапуск RabbitMQ консюмера в Supervisor...');

        // Команда для перезапуска процесса
        $process = new Process(['supervisorctl', 'restart', 'rabbitmq-consumer']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->info('✅ Консюмер успешно перезапущен.');
    }
}
