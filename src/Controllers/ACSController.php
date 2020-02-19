<?php

namespace Askvortsov\FlarumSAML\Controllers;

use Askvortsov\FlarumSAML\Controllers\BaseSAMLController;
use Askvortsov\FlarumAuthSync\Models\AuthSyncEvent;
use Carbon\Carbon;
use Flarum\Forum\Auth\Registration;
use OneLogin\Saml2\Constants;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;

class ACSController extends BaseSAMLController implements RequestHandlerInterface
{

    public function handle(Request $request): Response
    {
        try {
            $saml = $this->auth(true);
        } catch (\Exception $e) {
            return new HtmlResponse("Invalid SAML Configuration: Check Settings");
        }
        try {
            $saml->processResponse();
        } catch (\Exception $e) {
            throw $e;
            return new HtmlResponse("Could not process response: " . $e->getMessage());
        }
        if (!empty($saml->getErrors())) {
            $errors = implode(', ', $saml->getErrors());
            return new HtmlResponse("Could not process response: " . $errors . ": " . $saml->getLastErrorReason());
        }
        if (!$saml->isAuthenticated()) {
            return new HtmlResponse("Authentication Failed");
        }

        $is_email_auth = $saml->getNameIdFormat() === Constants::NAMEID_EMAIL_ADDRESS;

        $attributes = [];
        foreach($saml->getAttributes() as $key => $val) {
            $attributes[$key] = $val[0];
        }

        if ($is_email_auth) {
            $email = filter_var($saml->getNameId(), FILTER_VALIDATE_EMAIL);
        } else {
            $email = filter_var($attributes['urn:oid:1.2.840.113549.1.9.1.1'], FILTER_VALIDATE_EMAIL);
            unset($attributes['urn:oid:1.2.840.113549.1.9.1.1']);
            if ( ! isset($email) ) {
                $email = filter_var($attributes['email'], FILTER_VALIDATE_EMAIL);
            }
        }

        if ( !isset($email) ) {
            return new HtmlResponse("Email not provided.");
        }

        $masquerade_attributes = [];
        foreach($attributes as $key => $attribute) {
            if ($key != "avatar" && $key != "bio" && $key != "groups") {
                $masquerade_attributes[$key] = $attribute;
            }
        }

        if ($this->extensions->isEnabled('askvortsov-auth-sync') && $this->settings->get('askvortsov-saml.sync_attributes', '')) {
            $event = new AuthSyncEvent();
            $event->email=$email;
            $event->attributes = json_encode([
                "avatar" => $saml->getAttribute('avatar')[0],
                "bio" => $saml->getAttribute('bio')[0],
                "groups" => explode(",", $saml->getAttribute('groups')[0]),
                "masquerade_attributes" => $masquerade_attributes
            ]);
            $event->time = Carbon::now();
            $event->save();
        }

        return $this->response->make(
            'saml-sso',
            $saml->getNameId(),
            function (Registration $registration) use ($saml, $email) {
                $registration
                    ->provideTrustedEmail($email)
                    ->provideAvatar($saml->getAttribute('avatar')[0])
                    ->suggestUsername("")
                    ->setPayload([]);
            }
        );
    }
}
