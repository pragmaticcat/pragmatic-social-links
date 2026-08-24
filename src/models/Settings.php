<?php

namespace pragmatic\sociallinks\models;

use craft\base\Model;

class Settings extends Model
{
    public bool $showCodeExamples = true;
    public string $defaultRenderVariant = 'text';

    public function rules(): array
    {
        return [
            [['showCodeExamples'], 'boolean'],
            [['defaultRenderVariant'], 'string'],
            [['defaultRenderVariant'], 'in', 'range' => ['text', 'icons']],
        ];
    }
}
