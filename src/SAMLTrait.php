<?php

/*
 * This file is part of askvortsov/flarum-saml
 *
 *  Copyright (c) 2021 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Askvortsov\FlarumSAML;

use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\IdPMetadataParser;
use OneLogin\Saml2\Settings;

trait SAMLTrait
{
    protected function auth(): Auth
    {
        $samlSettings = $this->compileSettingsArray(true);

        return new Auth($samlSettings);
    }

    protected function compileSettingsArray(bool $incorporateIdpMetadata = true): array
    {
        /** @var SettingsRepositoryInterface */
        $settings = resolve(SettingsRepositoryInterface::class);

        /** @var UrlGenerator */
        $url = resolve(UrlGenerator::class);

        $samlSettings = [];

        if ($incorporateIdpMetadata) {
            $samlSettings = $this->incorporateIdpMetadata($samlSettings);
        }

        $samlSettings['sp'] = [
            'entityId'                 => $url->to('forum')->route('askvortsov-saml.metadata'),
            'assertionConsumerService' => [
                'url' => $url->to('forum')->route('askvortsov-saml.acs'),
            ],
            'singleLogoutService'      => [
                'url' => $url->to('forum')->route('askvortsov-saml.logout'),
            ],
            'NameIDFormat'              => $settings->get('askvortsov-saml.nameid_format', 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress'),
            'privateKey'                => $settings->get('askvortsov-saml.x509_key'),
            'x509cert'                  => $settings->get('askvortsov-saml.x509_cert'),
        ];
        $samlSettings['security'] = [
            'authnRequestsSigned'     => $settings->get('askvortsov-saml.authn_requests_signed', false),
            'logoutRequestSigned'     => $settings->get('askvortsov-saml.logout_request_signed', false),
            'logoutResponseSigned'    => $settings->get('askvortsov-saml.logout_response_signed', false),
            'signMetadata'            => $settings->get('askvortsov-saml.sign_metadata', false),
            'wantAssertionsEncrypted' => $settings->get('askvortsov-saml.want_assertions_encrypted', false),
            'wantAssertionsSigned'    => $settings->get('askvortsov-saml.want_assertions_signed', true),
            'wantMessagesSigned'      => $settings->get('askvortsov-saml.want_messages_signed', true),
        ];

        return $samlSettings;
    }

    protected function packageSettings($samlSettings, $validateSpOnly = true): Settings
    {
        return new Settings($samlSettings, $validateSpOnly);
    }

    private function incorporateIdpMetadata($samlSettings): array
    {
        /** @var SettingsRepositoryInterface */
        $settings = resolve(SettingsRepositoryInterface::class);

        $idp_metadata_url = trim($settings->get('askvortsov-saml.idp_metadata_url', ''));
        $idp_xml = trim($settings->get('askvortsov-saml.idp_metadata', ''));

        if (!empty($idp_metadata_url)) {
            $metadataSettings = IdPMetadataParser::parseRemoteXML($idp_metadata_url);
        } elseif (!empty($idp_xml)) {
            $metadataSettings = IdPMetadataParser::parseXML($idp_xml);
        } else {
            throw new \RuntimeException('Either a metadata URL or XML must be provided');
        }

        return IdPMetadataParser::injectIntoSettings($samlSettings, $metadataSettings);
    }
}
