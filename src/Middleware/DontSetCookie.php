<?php

/*
 * This file is part of askvortsov/flarum-saml
 *
 *  Copyright (c) 2021 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Askvortsov\FlarumSAML\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;

class DontSetCookie implements Middleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $onAcs = $request->getUri()->getPath() === '/auth/saml/acs' && $request->getMethod() === 'POST';
        $justLoggedIn = strpos($response->getHeaderLine('SET-COOKIE'), 'flarum_remember') !== false;
        if ($onAcs && !$justLoggedIn) {
            return $response->withoutHeader('SET-COOKIE');
        }

        return $response;
    }
}
