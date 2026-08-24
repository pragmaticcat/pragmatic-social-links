<?php

namespace pragmatic\sociallinks\models;

use craft\base\Model;

class SocialLinkItem extends Model
{
    public string $network = '';
    public string $title = '';
    public string $url = '';

    public function rules(): array
    {
        return [
            [['network', 'title', 'url'], 'string'],
            [['title'], 'trim'],
            [['url'], 'trim'],
        ];
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return [
            'network' => $this->network,
            'title' => $this->title,
            'url' => $this->url,
        ];
    }
}
