<?php

declare(strict_types=1);

namespace App\Exceptions;

class MicroserviceException extends \Exception
{
    private ?array $transferTrace = null;
    private ?array $errors = null;
    private int $statusCode;

    public function __construct(string $message = "", int $statusCode = 500, ?array $errors = null, ?array $transferTrace = null)
    {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
        $this->transferTrace = $transferTrace;
    }

    public function getTransferTrace(): ?array
    {
        return $this->transferTrace;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
