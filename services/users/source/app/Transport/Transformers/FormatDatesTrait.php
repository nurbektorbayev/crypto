<?php

declare(strict_types=1);

namespace App\Transport\Transformers;

trait FormatDatesTrait
{
    protected function isodate(\Carbon\Carbon $date = null): ?string
    {
        return $date?->copy()->setTimezone('UTC')->toIso8601String();
    }
}
