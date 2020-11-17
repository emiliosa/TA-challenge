<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use Tests\TestCase;
use App\Rules\TagsRule as TagsRuleSubject;

class TagsRule extends TestCase
{
    protected TagsRuleSubject $tagsRuleSubject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagsRuleSubject = app(TagsRuleSubject::class);
    }

    /**
     * @test
     * @covers TagsRule::passes()
     */
    public function tags_validation_passes()
    {
        $this->assertTrue($this->tagsRuleSubject->passes("test", ['name']));
        $this->assertTrue($this->tagsRuleSubject->passes("test", ["passengers_count"]));
        $this->assertTrue($this->tagsRuleSubject->passes("test", ["films_count"]));

        $this->assertFalse($this->tagsRuleSubject->passes("test", ["test"]));
        $this->assertFalse($this->tagsRuleSubject->passes("test", ["name", "passengers_count"]));
    }

    /**
     * @test
     * @covers TagsRule::message()
     */
    public function tags_validation_message()
    {
        $expectedTagsString = implode(',', $this->tagsRuleSubject->getTags());
        $this->assertEquals("Bad tags, expected [{$expectedTagsString}]", $this->tagsRuleSubject->message());
    }
}
