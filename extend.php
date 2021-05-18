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

use Askvortsov\FlarumSAML\Listeners\InitiateSLO;
use Askvortsov\FlarumSAML\Middleware\DontSetCookie;
use Flarum\Extend;
use Flarum\Http\Middleware\StartSession;
use Flarum\User\Event\LoggedOut;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Settings())
        ->serializeToForum('onlyUseSaml', 'askvortsov-saml.only_option', 'boolval'),

    (new Extend\Routes('forum'))
        ->get('/auth/saml/metadata', 'askvortsov-saml.metadata', Controllers\MetadataController::class)
        ->get('/auth/saml/login', 'askvortsov-saml.login', Controllers\LoginController::class)
        ->get('/auth/saml/logout', 'askvortsov-saml.logout', Controllers\LogoutController::class)
        ->post('/auth/saml/acs', 'askvortsov-saml.acs', Controllers\ACSController::class),

    (new Extend\Event())
        ->listen(LoggedOut::class, InitiateSLO::class),

    (new Extend\Csrf())
        ->exemptRoute('askvortsov-saml.acs'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Middleware('forum'))
        ->insertBefore(StartSession::class, DontSetCookie::class),
];
