<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class TagsRule
 * @package App\Rules
 * @TODO allow to use it with multiple tags
 */
class TagsRule implements Rule
{
    protected array $tags;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tags = ['name','passengers_count','films_count'];
    }

    /**
     * Only 1 tag allowed and it must exists inside $this->tags.
     * @inheritDoc
     */
    public function passes($attribute, $values)
    {
        return count($values) === 1 && array_intersect(array_values($values), $this->tags) === array_values($values);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $expectedTagsString = implode(',', $this->tags);
        return "Bad tags, expected [{$expectedTagsString}]";
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
