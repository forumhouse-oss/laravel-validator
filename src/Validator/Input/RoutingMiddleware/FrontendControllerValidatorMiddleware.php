<?php namespace FHTeam\LaravelValidator\Validator\Input\RoutingMiddleware;

use Closure;
use FHTeam\LaravelValidator\Validator\Input\AbstractRedirectingInputValidator;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;

/**
 * Class FrontendControllerValidatorMiddleware
 *
 * @package FHTeam\LaravelValidator\Middleware
 */
class FrontendControllerValidatorMiddleware extends AbstractRedirectingInputValidator implements Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $valid = $this->isThisValid();
        if (!$valid) {
            return $this->getRedirect();
        }

        return null;
    }

    /**
     * @param Factory    $validatorFactory
     * @param Request    $request
     * @param Router     $router
     * @param Redirector $redirector
     */
    public function __construct(Factory $validatorFactory, Request $request, Router $router, Redirector $redirector)
    {
        parent::__construct($validatorFactory, $request, $router, $redirector);
    }
}
