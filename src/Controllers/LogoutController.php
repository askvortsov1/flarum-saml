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

use Askvortsov\FlarumSAML\SAMLTrait;
use Flarum\User\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use OneLogin\Saml2\Constants;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class LogoutController implements RequestHandlerInterface
{
    use SAMLTrait;
    
    public function handle(Request $request): Response
    {
        try {
            $auth = $this->auth(true);
        } catch (\Exception $e) {
            resolve('log')->error($e->getMessage());

            return new HtmlResponse('Invalid SAML Configuration: Check Settings');
        }

        $auth->processSLO();

        $is_email_auth = $auth->getNameIdFormat() === Constants::NAMEID_EMAIL_ADDRESS;

        $attributes = [];
        foreach ($auth->getAttributes() as $key => $val) {
            $attributes[$key] = $val[0];
        }

        if ($is_email_auth) {
            $email = filter_var($auth->getNameId(), FILTER_VALIDATE_EMAIL);
        } else {
            $email = filter_var($attributes['urn:oid:1.2.840.113549.1.9.1.1'], FILTER_VALIDATE_EMAIL);
            unset($attributes['urn:oid:1.2.840.113549.1.9.1.1']);
            if (!isset($email)) {
                $email = filter_var($attributes['email'], FILTER_VALIDATE_EMAIL);
            }
        }

        if (!isset($email)) {
            throw new ModelNotFoundException();
        }

        $user = User::where('email', $email)->first();

        $user->accessTokens()->delete();

        return new EmptyResponse();
    }
}
