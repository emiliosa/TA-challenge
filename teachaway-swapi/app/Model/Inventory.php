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

    protected $table = 'inventory';
    protected $fillable = [
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
        $attributes['payload'] = json_decode($this->payload);

        return $attributes;
    }
}
