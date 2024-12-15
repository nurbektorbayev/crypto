<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class FormattedJSONResponse extends JsonResponse
{
    public static function list($data, $message = '', $code = self::HTTP_OK): self
    {
        $meta = [
            'status_code' => $code,
            'message' => $message,
            'pagination' => (isset($data['meta']['pagination'])) ? $data['meta']['pagination'] : [],
        ];

        if (isset($data['meta'])) {
            $meta = array_merge($meta, $data['meta']);
        }

        $data = [
            'meta' => $meta,
            'data' => $data['data'],
        ];

        return new self($data, $code);
    }

    public static function show($data, $message = '', array $meta = []): self
    {
        $data = [
            'meta' => [
                'status_code' => self::HTTP_OK,
                'message' => $message,
            ],
            'data' => $data,
        ];

        $data['meta'] = array_merge($data['meta'], $meta);

        return new self($data, self::HTTP_OK);
    }

    public static function created($data, $message = 'Resource created.', array $meta = []): self
    {
        $data = [
            'meta' => [
                'status_code' => self::HTTP_CREATED,
                'message' => $message,
            ],
            'data' => $data,
        ];

        $data['meta'] = array_merge($data['meta'], $meta);

        return new self($data, self::HTTP_CREATED);
    }

    public static function updated($data, $message = 'Resource updated.', array $meta = []): self
    {
        $data = [
            'meta' => [
                'status_code' => self::HTTP_OK,
                'message' => $message,
            ],
            'data' => $data,
        ];

        $data['meta'] = array_merge($data['meta'], $meta);

        return new self($data, self::HTTP_OK);
    }

    public static function deleted($data = [], $message = 'Resource deleted.', array $meta = []): self
    {
        $data = [
            'meta' => [
                'status_code' => self::HTTP_OK,
                'message' => $message,
            ],
            'data' => $data,
        ];

        $data['meta'] = array_merge($data['meta'], $meta);

        return new self($data, self::HTTP_OK);
    }

    public static function error($code, $message = 'Error occurred.', array $data = [], array $meta = []): self
    {
        $data = [
            'meta' => [
                'status_code' => $code,
                'message' => $message,
            ],
            'data' => $data,
        ];

        $data['meta'] = array_merge($data['meta'], $meta);

        return new self($data, $code);
    }
}
