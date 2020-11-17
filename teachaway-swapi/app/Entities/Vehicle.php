<?php

declare(strict_types=1);

namespace App\Entities;

use App\Concerns\Searchable;

/**
 * Class Vehicle
 * @package App\Entities
 * @method static array getSearchable()
 */
class Vehicle
{
    use Searchable;

    /**
     * @var array $searchable
     */
    protected static array $searchable = [
        "name",
        "model",
        "manufacturer",
        "cost_in_credits",
        "length",
        "max_atmosphering_speed",
        "crew",
        "passengers",
        "cargo_capacity",
        "consumables",
        "vehicle_class",
        "pilots",
        "films",
    ];
}
