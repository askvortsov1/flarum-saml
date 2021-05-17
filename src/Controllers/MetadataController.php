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
use Laminas\Diactoros\Response\XmlResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class MetadataController implements RequestHandlerInterface
{
    use SAMLTrait;

    public function handle(Request $request): Response
    {
        $useIdpInfo = false;
        $settingsArr = $this->compileSettingsArray($useIdpInfo);
        $settings = $this->packageSettings($settingsArr, !$useIdpInfo);

        $metadata = $settings->getSPMetadata();

        if (($errors = $settings->validateMetadata($metadata))) {
            throw new \Exception($errors);
        }

        return new XmlResponse($metadata);
    }
}
