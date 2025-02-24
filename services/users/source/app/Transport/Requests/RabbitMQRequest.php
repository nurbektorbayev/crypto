<?php

declare(strict_types=1);

namespace App\Transport\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Precognition;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class RabbitMQRequest extends FormRequest
{
    private const MAX_PER_PAGE = 50;

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(new JsonResponse(
            [
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ],
            422
        ));
    }

    public function getPerPage(int $defaultPerPage = 10): int
    {
        $perPage = (int)$this->query('per_page', $defaultPerPage);

        return min($perPage, $this->getMaxPerPage());
    }

    public function getMaxPerPage(): int
    {
        return self::MAX_PER_PAGE;
    }

    public function getPage($defaultPage = null): ?int
    {
        return $this->get('page', $defaultPage) ?: 1;
    }

    public function transportValidation(): void
    {
        $this->prepareForValidation();

        $instance = $this->getValidatorInstance();

        if ($this->isPrecognitive()) {
            $instance->after(Precognition::afterValidationHook($this));
        }

        if ($instance->fails()) {
            $this->failedValidation($instance);
        }

        $this->passedValidation();
    }
}
