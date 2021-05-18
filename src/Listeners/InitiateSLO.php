<?php

/*
 * This file is part of askvortsov/flarum-saml
 *
 *  Copyright (c) 2021 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Askvortsov\FlarumSAML\Listeners;

use Askvortsov\FlarumSAML\SAMLTrait;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Event\LoggedOut;
use Laminas\Diactoros\Response\HtmlResponse;
use OneLogin\Saml2\Error;
use Psr\Log\LoggerInterface;

class InitiateSLO
{
    use SAMLTrait;
    
    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(LoggerInterface $log, SettingsRepositoryInterface $settings)
    {
        $this->log = $log;
        $this->settings = $settings;
    }

    public function handle(LoggedOut $event)
    {
        try {
            $auth = $this->auth(true);
        } catch (\Exception $e) {
            $this->log->error($e->getMessage());

            return new HtmlResponse('Invalid SAML Configuration: Check Settings');
        }

        if (!$this->settings->get('askvortsov-saml.slo')) {
            return;
        }

        try {
            $auth->logout(null, [], $event->user->email);
        } catch (Error $e) {
            if ($e->getCode() === Error::SAML_SINGLE_LOGOUT_NOT_SUPPORTED) {
                return;
            }

            throw $e;
        }
    }
}
