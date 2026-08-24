<?php

namespace pragmatic\sociallinks\assetbundles\sociallinks;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SocialLinksAsset extends AssetBundle
{
    public function init(): void
    {
        $this->sourcePath = __DIR__ . '/dist';
        $this->depends = [
            CpAsset::class,
        ];
        $this->js = [
            'js/social-links.js',
        ];

        parent::init();
    }
}
