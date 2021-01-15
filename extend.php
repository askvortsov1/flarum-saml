<?php

/*
 * This file is part of askvortsov/flarum-saml
 *
 *  Copyright (c) 2020 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Askvortsov\FlarumSAML;

use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Settings())
        ->serializeToForum('onlyUseSaml', 'askvortsov-saml.only_option', function ($val) {
            return (bool) $val;
        }),

    (new Extend\Routes('forum'))
        ->get('/auth/saml/metadata', 'askvortsov-saml.metadata', Controllers\MetadataController::class)
        ->get('/auth/saml/login', 'askvortsov-saml.login', Controllers\LoginController::class)
        ->get('/auth/saml/logout', 'askvortsov-saml.logout', Controllers\LogoutController::class)
        ->post('/auth/saml/acs', 'askvortsov-saml.acs', Controllers\ACSController::class),

    (new Extend\Csrf())
        ->exemptRoute('askvortsov-saml.acs'),

    new Extend\Locales(__DIR__.'/resources/locale'),
];
