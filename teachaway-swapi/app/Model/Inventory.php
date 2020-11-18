<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasTimestamps;

    const UNIT_TYPE_VEHICLE = 'vehicle';
    const UNIT_TYPE_STARSHIP = 'starship';
    const CRITERIA_PM = 'partial_match';
    const CRITERIA_GT = 'greater_than';
    const CRITERIA_EQ = 'equal';
    const CRITERIA_LT = 'lower_than';
    const CRITERIA_GTE = 'greater_than_or_equal';
    const CRITERIA_LTE = 'lower_than_or_equal';
    CONST UNIT_TYPE_STARSHIP_NO_TAGS = self::UNIT_TYPE_STARSHIP . '_no_tags';
    CONST UNIT_TYPE_VEHICLE_NO_TAGS = self::UNIT_TYPE_VEHICLE . '_no_tags';
    CONST UNIT_TYPE_STARSHIP_EMPTY = self::UNIT_TYPE_STARSHIP . '_empty';
    CONST UNIT_TYPE_VEHICLE_EMPTY = self::UNIT_TYPE_VEHICLE . '_empty';

    protected $table = 'inventory';
    protected $fillable = [
        'id',
        'unit_type',
        'criteria',
        'tag',
        'count',
        'payload',
    ];
    protected $casts = [
        'count' => 'int'
    ];

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $attributes = parent::toArray();
        $attributes['payload'] = $this->payload !== null
            ? json_decode($this->payload, true)
            : [];

        return $attributes;
    }
}
