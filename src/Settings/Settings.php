<?php

namespace Askvortsov\FlarumSAML\Settings;

use Flarum\Api\Event\Serializing;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Container\Container;

class Settings implements ExtenderInterface
{
    public function extend(Container $container, Extension $extension = null)
    {
        $container['events']->listen(Serializing::class, [$this, 'settings']);
    }

    public function settings(Serializing $event)
    {
        if ($event->serializer instanceof ForumSerializer) {
            /**
             * @var $settings SettingsRepositoryInterface
             */
            $settings = app(SettingsRepositoryInterface::class);

            $event->attributes += [
                'emojioneAreaEnableSearch' => (bool) $settings->get('flarum-saml.enable-search', true),
                'emojioneAreaEnableRecent' => (bool) $settings->get('flarum-saml.enable-recent', true),
                'emojioneAreaEnableTones' => (bool) $settings->get('flarum-saml.enable-tones', true),
                'emojioneAreaFiltersPositionBottom' => (bool) $settings->get('flarum-saml.filters-position-bottom', false),
                'emojioneAreaSearchPositionBottom' => (bool) $settings->get('flarum-saml.search-position-bottom', false),
                'emojioneAreaHideFlarumButton' => (bool) $settings->get('flarum-saml.hide-flarum-button', true),
            ];
        }
    }
}
