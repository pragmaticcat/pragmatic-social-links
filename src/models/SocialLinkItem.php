<?php

namespace pragmatic\sociallinks\models;

use craft\base\Model;

class SocialLinkItem extends Model
{
    public string $network = '';
    public string $url = '';

    public function rules(): array
    {
        return [
            [['network', 'url'], 'string'],
            [['url'], 'trim'],
        ];
    }

    public function toArray(array $fields = [], array $expand = [], bool $recursive = true): array
    {
        return [
            'network' => $this->network,
            'url' => $this->url,
        ];
    }
}
