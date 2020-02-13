<?php

namespace Askvortsov\FlarumSAML\Controllers;

use Askvortsov\FlarumSAML\Controllers\BaseSAMLController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\XmlResponse;


class MetadataController extends BaseSAMLController implements RequestHandlerInterface
{
    public function handle(Request $request): Response
    {
        try {
            $auth     = $this->auth(false);
        } catch (\Exception $e) {
            return new HtmlResponse("Invalid SAML Configuration: Check Settings");
        }
        $settings = $auth->getSettings();
        $metadata = null;
        try {
            $metadata = $settings->getSPMetadata();
            $errors   = $settings->validateMetadata($metadata);
        } catch (\Exception $e) {
            $errors = $e->getMessage();
        }

        if ($errors) {
            //throw new \Exception($errors);
            return new XMLResponse($errors);
        }
        return new XmlResponse($metadata);
    }
}
