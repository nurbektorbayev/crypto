<?php

declare(strict_types=1);

namespace App\Transport\Message;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Validation\Validator;

class RabbitMQMessagePayload implements Arrayable
{
    public function __construct(private array|\Throwable|Validator $data)
    {
    }

    public function toArray(): array
    {
        if ($this->data instanceof \Throwable) {
            return [
                'error_code' => $this->data->getCode(),
                'error_message' => $this->data->getMessage(),
                'trace' => $this->data->getTrace(),
            ];
        }

        if ($this->data instanceof Validator) {
            return [
                'error_code' => 422,
                'error_message' => $this->data->getMessageBag()->first(),
                'errors' => $this->data->errors()->toArray(),
                'trace' => [],
            ];
        }

        return $this->data;
    }
}
