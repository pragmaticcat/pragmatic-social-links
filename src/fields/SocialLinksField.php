<?php

namespace pragmatic\sociallinks\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Html;
use craft\helpers\Json;
use pragmatic\sociallinks\assetbundles\sociallinks\SocialLinksAsset;
use pragmatic\sociallinks\models\SocialLinkItem;
use pragmatic\sociallinks\models\SocialLinksFieldValue;
use yii\db\Schema;

class SocialLinksField extends Field
{
    public string $translationMethod = self::TRANSLATION_METHOD_SITE;

    public static function displayName(): string
    {
        return Craft::t('pragmatic-social-links', 'Social Links');
    }

    public static function icon(): string
    {
        return 'share';
    }

    public static function dbType(): array|string|null
    {
        return Schema::TYPE_JSON;
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_JSON;
    }

    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        if ($value instanceof SocialLinksFieldValue) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = Json::decodeIfJson($value);
            if (is_array($decoded)) {
                $value = $decoded;
            }
        }

        if (!is_array($value)) {
            return new SocialLinksFieldValue(['items' => []]);
        }

        $items = [];
        foreach ($value as $row) {
            if (!is_array($row)) {
                continue;
            }

            $network = trim((string)($row['network'] ?? ''));
            $url = trim((string)($row['url'] ?? ''));

            if ($network === '' && $url === '') {
                continue;
            }

            $items[] = new SocialLinkItem([
                'network' => $network,
                'url' => $url,
            ]);
        }

        return new SocialLinksFieldValue(['items' => $items]);
    }

    public function serializeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        $normalized = $this->normalizeValue($value, $element);

        if (!$normalized instanceof SocialLinksFieldValue) {
            return [];
        }

        return $normalized->asArray();
    }

    public function getSearchKeywords(mixed $value, ElementInterface $element): string
    {
        $normalized = $this->normalizeValue($value, $element);
        if (!$normalized instanceof SocialLinksFieldValue) {
            return '';
        }

        return implode(' ', array_map(static function(SocialLinkItem $item): string {
            $definition = self::socialNetworkDefinition($item->network);
            return trim(($definition['label'] ?? $item->network) . ' ' . $item->url);
        }, $normalized->all()));
    }

    public function getInputHtml(mixed $value, ?ElementInterface $element = null, bool $inline = false): string
    {
        $normalized = $this->normalizeValue($value, $element);
        if (!$normalized instanceof SocialLinksFieldValue) {
            $normalized = new SocialLinksFieldValue(['items' => []]);
        }

        $id = Html::id($this->handle);
        $view = Craft::$app->getView();
        $namespacedId = $view->namespaceInputId($id);
        $inputName = $view->namespaceInputName($this->handle);

        $view->registerAssetBundle(SocialLinksAsset::class);

        return $view->renderTemplate('pragmatic-social-links/fields/input', [
            'field' => $this,
            'value' => $normalized,
            'id' => $id,
            'namespacedId' => $namespacedId,
            'socialNetworks' => self::socialNetworkOptions(),
            'socialNetworkMap' => self::socialNetworks(),
        ]);
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('pragmatic-social-links/fields/settings', [
            'field' => $this,
        ]);
    }

    public static function socialNetworkOptions(): array
    {
        return array_map(
            static fn(array $network): array => ['label' => $network['label'], 'value' => $network['handle']],
            self::socialNetworks()
        );
    }

    public static function socialNetworkDefinition(string $handle): ?array
    {
        foreach (self::socialNetworks() as $network) {
            if ($network['handle'] === $handle) {
                return $network;
            }
        }

        return null;
    }

    public static function socialNetworks(): array
    {
        static $networks = null;
        if ($networks !== null) {
            return $networks;
        }

        $networks = [
            ['handle' => 'facebook', 'label' => 'Facebook', 'icon' => self::iconSvg('F')],
            ['handle' => 'youtube', 'label' => 'YouTube', 'icon' => self::iconSvg('Y')],
            ['handle' => 'instagram', 'label' => 'Instagram', 'icon' => self::iconSvg('I')],
            ['handle' => 'tiktok', 'label' => 'TikTok', 'icon' => self::iconSvg('T')],
            ['handle' => 'wechat', 'label' => 'WeChat', 'icon' => self::iconSvg('W')],
            ['handle' => 'whatsapp', 'label' => 'WhatsApp', 'icon' => self::iconSvg('W')],
            ['handle' => 'messenger', 'label' => 'Messenger', 'icon' => self::iconSvg('M')],
            ['handle' => 'telegram', 'label' => 'Telegram', 'icon' => self::iconSvg('T')],
            ['handle' => 'snapchat', 'label' => 'Snapchat', 'icon' => self::iconSvg('S')],
            ['handle' => 'x', 'label' => 'X / Twitter', 'icon' => self::iconSvg('X')],
            ['handle' => 'linkedin', 'label' => 'LinkedIn', 'icon' => self::iconSvg('L')],
            ['handle' => 'pinterest', 'label' => 'Pinterest', 'icon' => self::iconSvg('P')],
            ['handle' => 'tripadvisor', 'label' => 'Tripadvisor', 'icon' => self::iconSvg('T')],
            ['handle' => 'booking', 'label' => 'Booking.com', 'icon' => self::iconSvg('B')],
            ['handle' => 'google-business-profile', 'label' => 'Google Business Profile', 'icon' => self::iconSvg('G')],
            ['handle' => 'airbnb', 'label' => 'Airbnb', 'icon' => self::iconSvg('A')],
            ['handle' => 'reddit', 'label' => 'Reddit', 'icon' => self::iconSvg('R')],
            ['handle' => 'yelp', 'label' => 'Yelp', 'icon' => self::iconSvg('Y')],
            ['handle' => 'discord', 'label' => 'Discord', 'icon' => self::iconSvg('D')],
            ['handle' => 'threads', 'label' => 'Threads', 'icon' => self::iconSvg('@')],
            ['handle' => 'twitch', 'label' => 'Twitch', 'icon' => self::iconSvg('T')],
            ['handle' => 'line', 'label' => 'LINE', 'icon' => self::iconSvg('L')],
            ['handle' => 'qq', 'label' => 'QQ', 'icon' => self::iconSvg('Q')],
            ['handle' => 'weibo', 'label' => 'Weibo', 'icon' => self::iconSvg('W')],
            ['handle' => 'tumblr', 'label' => 'Tumblr', 'icon' => self::iconSvg('T')],
            ['handle' => 'viber', 'label' => 'Viber', 'icon' => self::iconSvg('V')],
            ['handle' => 'mastodon', 'label' => 'Mastodon', 'icon' => self::iconSvg('M')],
            ['handle' => 'bluesky', 'label' => 'Bluesky', 'icon' => self::iconSvg('B')],
            ['handle' => 'medium', 'label' => 'Medium', 'icon' => self::iconSvg('M')],
            ['handle' => 'quora', 'label' => 'Quora', 'icon' => self::iconSvg('Q')],
            ['handle' => 'flickr', 'label' => 'Flickr', 'icon' => self::iconSvg('F')],
            ['handle' => 'vimeo', 'label' => 'Vimeo', 'icon' => self::iconSvg('V')],
            ['handle' => 'expedia', 'label' => 'Expedia', 'icon' => self::iconSvg('E')],
            ['handle' => 'trivago', 'label' => 'Trivago', 'icon' => self::iconSvg('T')],
            ['handle' => 'kayak', 'label' => 'Kayak', 'icon' => self::iconSvg('K')],
            ['handle' => 'vrbo', 'label' => 'Vrbo', 'icon' => self::iconSvg('V')],
            ['handle' => 'github', 'label' => 'GitHub', 'icon' => self::iconSvg('G')],
            ['handle' => 'gitlab', 'label' => 'GitLab', 'icon' => self::iconSvg('G')],
            ['handle' => 'dribbble', 'label' => 'Dribbble', 'icon' => self::iconSvg('D')],
            ['handle' => 'behance', 'label' => 'Behance', 'icon' => self::iconSvg('B')],
            ['handle' => 'spotify', 'label' => 'Spotify', 'icon' => self::iconSvg('S')],
            ['handle' => 'soundcloud', 'label' => 'SoundCloud', 'icon' => self::iconSvg('S')],
            ['handle' => 'xing', 'label' => 'Xing', 'icon' => self::iconSvg('X')],
        ];

        return $networks;
    }

    private static function iconSvg(string $label): string
    {
        $safeLabel = Html::encode($label);

        return <<<SVG
<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
  <circle cx="12" cy="12" r="11" fill="currentColor"></circle>
  <text x="12" y="15.2" text-anchor="middle" font-size="10" font-family="Arial, sans-serif" fill="#fff">{$safeLabel}</text>
</svg>
SVG;
    }
}
