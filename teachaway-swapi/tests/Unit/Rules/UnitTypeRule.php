<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Model\Inventory;
use Tests\TestCase;
use App\Rules\UnitTypeRule as UnitTypeRuleSubject;

class UnitTypeRule extends TestCase
{
    protected UnitTypeRuleSubject $unitTypeRuleSubject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unitTypeRuleSubject = app(UnitTypeRuleSubject::class);
    }

    /**
     * @test
     * @covers TagsRule::passes()
     */
    public function tags_validation_passes()
    {
        $this->assertTrue($this->unitTypeRuleSubject->passes("test", Inventory::UNIT_TYPE_STARSHIP));
        $this->assertTrue($this->unitTypeRuleSubject->passes("test", Inventory::UNIT_TYPE_VEHICLE));

        $this->assertFalse($this->unitTypeRuleSubject->passes("test",
            [Inventory::UNIT_TYPE_STARSHIP, Inventory::UNIT_TYPE_VEHICLE]));
        $this->assertFalse($this->unitTypeRuleSubject->passes("test", "sarasa"));
    }

    /**
     * @test
     * @covers TagsRule::message()
     */
    public function tags_validation_message()
    {
        $expectedUnitTypesString = implode(',', $this->unitTypeRuleSubject->getUnitTypes());
        $this->assertEquals("Bad unit type, expected [{$expectedUnitTypesString}]",
            $this->unitTypeRuleSubject->message());
    }
}
