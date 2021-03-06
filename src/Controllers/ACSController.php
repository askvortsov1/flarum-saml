<?php

/*
 * This file is part of askvortsov/flarum-saml
 *
 *  Copyright (c) 2021 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Askvortsov\FlarumSAML\Controllers;

use Askvortsov\FlarumAuthSync\Models\AuthSyncEvent;
use Askvortsov\FlarumSAML\SAMLTrait;
use Carbon\Carbon;
use Flarum\Extension\ExtensionManager;
use Flarum\Forum\Auth\Registration;
use Flarum\Forum\Auth\ResponseFactory;
use Flarum\Settings\SettingsRepositoryInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use OneLogin\Saml2\Constants;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class ACSController implements RequestHandlerInterface
{
    use SAMLTrait;

    /**
     * @var ResponseFactory
     */
    protected $response;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var ExtensionManager
     */
    protected $extensions;

    public function __construct(ResponseFactory $response, SettingsRepositoryInterface $settings, ExtensionManager $extensions)
    {
        $this->response = $response;
        $this->settings = $settings;
        $this->extensions = $extensions;
    }

    public function handle(Request $request): Response
    {
        try {
            $saml = $this->auth(true);
        } catch (\Exception $e) {
            resolve('log')->error($e->getMessage());

            return new HtmlResponse('Invalid SAML Configuration: Check Settings');
        }

        try {
            $saml->processResponse();
        } catch (\Exception $e) {
            resolve('log')->error($e->getMessage());

            return new HtmlResponse('Could not process response: '.$e->getMessage());
        }
        if (!empty($saml->getErrors())) {
            $errors = implode(', ', $saml->getErrors());

            return new HtmlResponse('Could not process response: '.$errors.': '.$saml->getLastErrorReason());
        }
        if (!$saml->isAuthenticated()) {
            return new HtmlResponse('Authentication Failed');
        }

        $is_email_auth = $saml->getNameIdFormat() === Constants::NAMEID_EMAIL_ADDRESS;

        $attributes = [];
        foreach ($saml->getAttributes() as $key => $val) {
            $attributes[$key] = $val[0];
        }

        if ($is_email_auth) {
            $email = filter_var($saml->getNameId(), FILTER_VALIDATE_EMAIL);
        } else {
            $email = filter_var($attributes['urn:oid:1.2.840.113549.1.9.1.1'], FILTER_VALIDATE_EMAIL);
            unset($attributes['urn:oid:1.2.840.113549.1.9.1.1']);
            if (!isset($email)) {
                $email = filter_var($attributes['email'], FILTER_VALIDATE_EMAIL);
            }
        }

        if (!isset($email)) {
            return new HtmlResponse('Email not provided.');
        }

        $masquerade_attributes = [];
        foreach ($attributes as $key => $attribute) {
            if ($key != 'avatar' && $key != 'bio' && $key != 'groups') {
                $masquerade_attributes[$key] = $attribute;
            }
        }

        $avatar = $saml->getAttribute('avatar')[0];

        if ($this->extensions->isEnabled('askvortsov-auth-sync') && $this->settings->get('askvortsov-saml.sync_attributes', false)) {
            $event = new AuthSyncEvent();
            $event->email = $email;
            $event->attributes = json_encode([
                'avatar'                => $avatar,
                'bio'                   => $saml->getAttribute('bio')[0],
                'groups'                => explode(',', $saml->getAttribute('groups')[0]),
                'masquerade_attributes' => $masquerade_attributes,
            ]);
            $event->time = Carbon::now();
            $event->save();
        }

        return $this->response->make(
            'saml-sso',
            $saml->getNameId(),
            function (Registration $registration) use ($avatar, $email) {
                $registration
                    ->provideTrustedEmail($email)
                    ->suggestUsername('')
                    ->setPayload([]);

                if ($avatar) {
                    $registration->provideAvatar($avatar);
                }
            }
        );
    }
}
