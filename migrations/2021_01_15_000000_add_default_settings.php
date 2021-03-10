<?php

/*
 * This file is part of askvortsov/flarum-saml
 *
 *  Copyright (c) 2021 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

use Flarum\Database\Migration;

return Migration::addSettings([
    'askvortsov-saml.want_assertions_signed' => true,
    'askvortsov-saml.want_messages_signed'   => true,
    'askvortsov-saml.nameid_format'          => 'askvortsov-saml.admin.options.nameid_format.emailAddress',
]);
