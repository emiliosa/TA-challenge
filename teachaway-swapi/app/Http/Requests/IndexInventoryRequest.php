<?php

namespace App\Http\Requests;

use App\Rules\TagsRule;
use App\Rules\UnitTypeRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @TODO enable tags rule with multiple tags config
     */
    public function rules()
    {
        return [
            'unit_type' => ['required', new UnitTypeRule()],
            // 'tags' => ['required', new TagsRule()],
        ];
    }
}
