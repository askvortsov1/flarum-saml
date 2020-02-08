<?php

// SAML SSO requires sending the SAMLResponse via POST. As of Beta 11, Flarum doesn't effectively support
// exempting routes from CSRF. This will be addressed in Beta 12 via middleware extenders, at which point this
// behemoth of code will become unnecessary, and will be removed. In the meantime, this provides a workaround.

namespace Askvortsov\FlarumSAML\Listener;


use Flarum\Foundation\Application;
use Flarum\Event\ConfigureMiddleware;
use Flarum\Http\Middleware as HttpMiddleware;
use Zend\Stratigility\MiddlewarePipe;
use Flarum\Foundation\ErrorHandling\Registry;
use Flarum\Foundation\ErrorHandling\Reporter;
use Flarum\Foundation\ErrorHandling\ViewFormatter;
use Flarum\Foundation\ErrorHandling\WhoopsFormatter;

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

class HijackedPipe {

    protected $pipe;
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function pipe() {
        $pipe = new MiddlewarePipe;

        $pipe->pipe(new HttpMiddleware\HandleErrors(
            $this->app->make(Registry::class),
            $this->app->inDebugMode() ? $this->app->make(WhoopsFormatter::class) : $this->app->make(ViewFormatter::class),
            $this->app->tagged(Reporter::class)
        ));


        $pipe->pipe($this->app->make(HttpMiddleware\ParseJsonBody::class));
        $pipe->pipe($this->app->make(HttpMiddleware\CollectGarbage::class));
        $pipe->pipe($this->app->make(HttpMiddleware\StartSession::class));
        $pipe->pipe($this->app->make(HttpMiddleware\RememberFromCookie::class));
        $pipe->pipe($this->app->make(HttpMiddleware\AuthenticateWithSession::class));

        // all of this boilerplate for this one line of code:
        $pipe->pipe(new CsrfExempt);

        $pipe->pipe($this->app->make(HttpMiddleware\CheckCsrfToken::class));
        $pipe->pipe($this->app->make(HttpMiddleware\SetLocale::class));
        $pipe->pipe($this->app->make(HttpMiddleware\ShareErrorsFromSession::class));

        event(new ConfigureMiddleware($pipe, 'forum'));
        return $pipe;
    }
}

class CsrfExemptWorkaround
{
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function subscribe()
    {
        $this->app->singleton('flarum.forum.middleware', function (Application $app) {
            $this->app = $app;
            return (new HijackedPipe($app))->pipe();
        });
    }
}
