<?php namespace FHTeam\LaravelValidator\Input\RoutingMiddleware;

use Closure;
use FHTeam\LaravelValidator\Input\AbstractInputValidator;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;

/**
 * Class ApiControllerValidatorMiddleware
 *
 * @package Middleware
 */
class ApiControllerValidatorMiddleware extends AbstractInputValidator implements Middleware
{
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var string Default key name in which to return a list of validation errors
     */
    protected $errorKeyName = 'validationErrors';

    /**
     * @param Factory         $validatorFactory
     * @param Request         $request
     * @param Router          $router
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        Factory $validatorFactory,
        Request $request,
        Router $router,
        ResponseFactory $responseFactory
    ) {
        parent::__construct($validatorFactory, $request, $router);
        $this->responseFactory = $responseFactory;
    }

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
            $result = $this->makeResponse($this->getFailedRules());

            return $this->responseFactory->json($result);
        }

        return null;
    }

    /**
     * @param array $failedRules
     *
     * @return array
     */
    protected function makeResponse(array $failedRules)
    {
        $result = [];
        Arr::set($result, $this->errorKeyName, $failedRules);

        return $result;
    }
}
