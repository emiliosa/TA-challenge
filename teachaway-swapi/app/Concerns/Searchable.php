<?php

declare(strict_types=1);

namespace App\Concerns;

trait Searchable
{
    /**
     * Get searchable values
     *
     * @return array
     */
    public static function getSearchable(): array {
        return static::$searchable;
    }
}
