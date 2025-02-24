<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\MicroserviceException;

class MicroserviceResponse
{
    private array $payload;

    public function __construct(array $body)
    {
        $this->payload = $body['payload'] ?? [];
    }

    public function isError(): bool
    {
        return isset($this->payload['error_code']);
    }

    public function getErrorCode(): ?int
    {
        $errorCode = null;
        if (isset($this->payload['error_code'])) {
            $errorCode = (int) $this->payload['error_code'];
        }

        if ($errorCode === 0) {
            $errorCode = 500;
        }

        return $errorCode;
    }

    public function buildException(): ?MicroserviceException
    {
        if (!$this->isError()) {
            return null;
        }

        return new MicroserviceException($this->getErrorMessage(), $this->getErrorCode(), $this->getErrors(), $this->getTrace());
    }

    public function getErrorMessage(): ?string
    {
        return $this->payload['error_message'] ?? null;
    }

    public function getErrors(): ?array
    {
        return $this->payload['errors'] ?? null;
    }

    public function getTrace(): ?array
    {
        return $this->payload['trace'] ?? null;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
