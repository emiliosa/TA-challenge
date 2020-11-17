<?php

namespace App\Rules;

use App\Model\Inventory;
use Illuminate\Contracts\Validation\Rule;

class UnitTypeRule implements Rule
{
    protected array $unitTypes;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->unitTypes = [Inventory::UNIT_TYPE_STARSHIP, Inventory::UNIT_TYPE_VEHICLE];
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, $this->unitTypes);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $expectedUnitTypesString = implode(',', $this->unitTypes);
        return "Bad unit type, expected [{$expectedUnitTypesString}]";
    }

    /**
     * @return array
     */
    public function getUnitTypes(): array
    {
        return $this->unitTypes;
    }
}
