<?php

/*
 * This file is part of askvortsov/flarum-saml
 *
 *  Copyright (c) 2020 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Askvortsov\FlarumSAML\Controllers;

use Flarum\Extension\ExtensionManager;
use Flarum\Forum\Auth\ResponseFactory;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\IdPMetadataParser;
use OneLogin\Saml2\Settings;

abstract class BaseSAMLController
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var ResponseFactory
     */
    protected $response;

    protected $extensions;

    public function __construct(ResponseFactory $response, SettingsRepositoryInterface $settings, ExtensionManager $extensions, UrlGenerator $url)
    {
        $this->response = $response;
        $this->settings = $settings;
        $this->extensions = $extensions;
        $this->url = $url;
    }

    public function auth(): Auth
    {
        static $instance;
        if (empty($instance)) {
            $settings = $this->compileSettingsArray(true);
            $instance = new Auth($settings);

            return $instance;
        } else {
            return $instance;
        }
    }

    public function compileSettingsArray(bool $incorporateIdpMetadata = true)
    {
        $settings = [];

        if ($incorporateIdpMetadata) {
            $settings = $this->incorporateIdpMetadata($settings);
        }

        $settings['sp'] = [
            'entityId'                 => $this->url->to('forum')->route('askvortsov-saml.metadata'),
            'assertionConsumerService' => [
                'url' => $this->url->to('forum')->route('askvortsov-saml.acs'),
            ],
            'singleLogoutService'      => [
                'url' => $this->url->to('forum')->route('askvortsov-saml.logout'),
            ],
            'NameIDFormat'             => $this->settings->get('askvortsov-saml.nameid_format', 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress'),
        ];
        $settings['security'] = [
            'wantMessagesSigned'   => $this->settings->get('askvortsov-saml.want_messages_signed', true),
            'wantAssertionsSigned' => $this->settings->get('askvortsov-saml.want_assertions_signed', true),
        ];

        return $settings;
    }

    public function packageSettings($settings, $validateSpOnly = true)
    {
        return new Settings($settings, $validateSpOnly);
    }

    public function incorporateIdpMetadata($settings)
    {
        try {
            /**
             * Filters the XML metadata for IdP authority.
             *
             * @return string XML string for IdP metadata
             */
            try {
                $idp_metadata_url = trim($this->settings->get('askvortsov-saml.idp_metadata_url', ''));
                $metadataSettings = IdPMetadataParser::parseRemoteXML($idp_metadata_url);
            } catch (\Exception $e) {
                $idp_xml = trim($this->settings->get('askvortsov-saml.idp_metadata', ''));
                $metadataSettings = IdPMetadataParser::parseXML($idp_xml);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return IdPMetadataParser::injectIntoSettings($settings, $metadataSettings);
    }
}
