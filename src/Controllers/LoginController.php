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

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class LoginController extends BaseSAMLController implements RequestHandlerInterface
{
    public function handle(Request $request): Response
    {
        try {
            $auth = $this->auth(true);
        } catch (\Exception $e) {
            resolve('log')->error($e->getMessage());
            
            return new HtmlResponse('Invalid SAML Configuration: Check Settings');
        }

        return $auth->login();
    }
}
