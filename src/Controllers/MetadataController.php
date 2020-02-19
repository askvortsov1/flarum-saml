<?php

namespace Askvortsov\FlarumSAML\Controllers;

use Askvortsov\FlarumSAML\Controllers\BaseSAMLController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\XmlResponse;


class MetadataController extends BaseSAMLController implements RequestHandlerInterface
{
    public function handle(Request $request): Response
    {
        $useIdpInfo = false;
        $settingsArr = $this->compileSettingsArray($useIdpInfo);
        $settings = $this->packageSettings($settingsArr, !$useIdpInfo);
        try {
            $metadata = $settings->getSPMetadata();
            $errors   = $settings->validateMetadata($metadata);
        } catch (\Exception $e) {
            $errors = $e->getMessage();
        }

        if ($errors) {
            throw new \Exception($errors);
        }
        return new XmlResponse($metadata);
    }
}
