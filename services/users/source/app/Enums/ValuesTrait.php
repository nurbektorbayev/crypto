<?php

declare(strict_types=1);

namespace App\Enums;

/*
 * Returns all values of Enum
 */
trait ValuesTrait
{
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }
}
