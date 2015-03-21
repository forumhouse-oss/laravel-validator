<?php namespace FHTeam\LaravelValidator\Input\RoutingMiddleware;

use FHTeam\LaravelValidator\Input\AbstractRedirectingInputValidator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;

/**
 * Class FrontendControllerValidatorMiddleware
 *
 * @package FHTeam\LaravelValidator\Middleware
 */
class FrontendControllerValidatorWhenResolved extends AbstractRedirectingInputValidator implements ValidatesWhenResolved
{
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

    /**
     * Validate request
     *
     * @return mixed
     */
    public function validate()
    {
        if (!$this->isThisValid()) {
            throw new HttpResponseException($this->getRedirect());
        }

        return null;
    }
}
