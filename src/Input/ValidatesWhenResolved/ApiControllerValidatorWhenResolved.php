<?php namespace FHTeam\LaravelValidator\Input\ValidatesWhenResolved;

use FHTeam\LaravelValidator\Input\AbstractInputValidator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;

/**
 * Class ApiControllerValidatorMiddleware
 *
 * @package Middleware
 */
class ApiControllerValidatorWhenResolved extends AbstractInputValidator implements ValidatesWhenResolved
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
     * @return null
     * @throws HttpResponseException
     */
    public function validate()
    {
        if (!$this->isThisValid()) {
            $result = $this->makeResponse($this->getFailedRules());

            throw new HttpResponseException($this->responseFactory->json($result));
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
