<?php

/*
 * This file is part of askvortsov/flarum-saml.
 *
 * Copyright (c) 2020 Alexander Skvortsov.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Askvortsov\FlarumSAML;

use Flarum\Extend;
use FoF\Components\Extend\AddFofComponents;

use Askvortsov\FlarumSAML\Controllers;
use Askvortsov\FlarumSAML\Listener;
use Illuminate\Contracts\Events\Dispatcher;


return [
    new AddFofComponents(),

    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),

    function (Dispatcher $events) {
        $events->subscribe(Listener\CsrfExemptWorkaround::class);
    },

    (new Extend\Routes('forum'))
        ->get('/auth/saml/metadata', 'askvortsov-saml.metadata', Controllers\MetadataController::class)
        ->get('/auth/saml/login', 'askvortsov-saml.login', Controllers\LoginController::class)
        ->get('/auth/saml/logout', 'askvortsov-saml.logout', Controllers\LogoutController::class)
        ->post('/auth/saml/acs', 'askvortsov-saml.acs', Controllers\ACSController::class),

    new Extend\Locales(__DIR__ . '/resources/locale')
];
