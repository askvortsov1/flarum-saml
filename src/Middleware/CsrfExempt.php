<?php

/*
 * This file is part of askvortsov/flarum-saml
 *
 *  Copyright (c) 2020 Alexander Skvortsov.
 *
 *  For detailed copyright and license information, please view the
 *  LICENSE file that was distributed with this source code.
 */

namespace Askvortsov\FlarumSAML\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class CsrfExempt implements Middleware
{
    public function process(Request $request, Handler $handler): Response
    {
        $path = $request->getUri()->getPath();
        if ($path === '/auth/saml/acs') {
            return $handler->handle($request->withAttribute('bypassCsrfToken', true));
        }

        return $handler->handle($request);
    }
}
