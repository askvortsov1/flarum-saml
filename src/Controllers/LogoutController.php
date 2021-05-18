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
use Flarum\Http\Rememberer;
use Flarum\Http\SessionAuthenticator;
use Flarum\Http\UrlGenerator;
use Flarum\User\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use OneLogin\Saml2\Constants;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class LogoutController implements RequestHandlerInterface
{
    use SAMLTrait;

    /**
     * @var SessionAuthenticator
     */
    protected $authenticator;

    /**
     * @var Rememberer
     */
    protected $rememberer;

    /**
     * @var UrlGenerator
     */
    protected $url;

    public function __construct(SessionAuthenticator $authenticator, Rememberer $rememberer, UrlGenerator $url)
    {
        $this->authenticator = $authenticator;
        $this->rememberer = $rememberer;
        $this->url = $url;
    }

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

        $session = $request->getAttribute('session');
        $url = Arr::get($request->getQueryParams(), 'return', $this->url->to('forum')->base());
        $response = new RedirectResponse($url);

        $actor = User::where('email', $email)->first();

        if ($session) {
            $this->authenticator->logOut($session);

            $actor->accessTokens()->delete();

            return $this->rememberer->forget($response);
        }

        $actor->accessTokens()->delete();

        return $response;
    }
}
