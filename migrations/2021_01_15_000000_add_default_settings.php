<?php

use Flarum\Database\Migration;

return Migration::addSettings([
    'askvortsov-saml.want_assertions_signed' => true,
    'askvortsov-saml.want_messages_signed' => true,
    'askvortsov-saml.nameid_format' => 'askvortsov-saml.admin.options.nameid_format.emailAddress'
]);
