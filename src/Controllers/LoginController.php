<?php

namespace Askvortsov\FlarumSAML\Controllers;

use Askvortsov\FlarumSAML\Controllers\BaseSAMLController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

class LoginController extends BaseSAMLController implements RequestHandlerInterface
{
    public function handle(Request $request): Response
    {
        try {
            $auth = $this->auth(true);
        } catch (\Exception $e) {
            return new HtmlResponse("Invalid SAML Configuration: Check Settings");
        }
        return $auth->login();
    }
}
