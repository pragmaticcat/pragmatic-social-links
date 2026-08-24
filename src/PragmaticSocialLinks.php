<?php

namespace pragmatic\sociallinks;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use pragmatic\sociallinks\fields\SocialLinksField;
use pragmatic\sociallinks\models\Settings;
use yii\base\Event;

class PragmaticSocialLinks extends Plugin
{
    public static PragmaticSocialLinks $plugin;

    public bool $hasCpSection = false;
    public string $schemaVersion = '1.0.0';

    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        Craft::$app->i18n->translations['pragmatic-social-links'] = [
            'class' => \yii\i18n\PhpMessageSource::class,
            'basePath' => __DIR__ . '/translations',
            'forceTranslation' => true,
            'fileMap' => [
                'pragmatic-social-links' => 'pragmatic-social-links.php',
            ],
        ];

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            static function(RegisterComponentTypesEvent $event): void {
                $event->types[] = SocialLinksField::class;
            }
        );
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('pragmatic-social-links/settings', [
            'settings' => $this->getSettings(),
            'socialNetworks' => SocialLinksField::socialNetworks(),
        ]);
    }
}
