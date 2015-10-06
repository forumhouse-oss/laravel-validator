<?php namespace FHTeam\LaravelValidator\Validator\Input\RoutingMiddleware;

use Closure;
use Exception;
use FHTeam\LaravelValidator\Validator\Input\AbstractInputValidator;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiControllerValidatorMiddleware
 *
 * @package Middleware
 */
class ApiControllerValidatorMiddleware extends AbstractInputValidator implements Middleware
{
    /**
     * Return rule names as errors on validation failure
     */
    const ERROR_FORMAT_RULES = "rules";

    /**
     * Return human readable messages on validation failure
     */
    const ERROR_FORMAT_MESSAGES = "messages";

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var string Default key name in which to return a list of validation errors
     */
    protected $errorKeyName = 'validationErrors';

    /**
     * @var int Do we want to see rule names or human readable messages on validation failure?
     */
    protected $errorFormat = self::ERROR_FORMAT_RULES;

    /**
     * @var int Default HTTP status for validator error response
     */
    protected $errorResponseStatus = Response::HTTP_OK;

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
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        if (!$this->isThisValid()) {
            $errorList = null;

            switch ($this->errorFormat) {
                case self::ERROR_FORMAT_RULES:
                    $errorList = $this->getFailedRules();
                    break;
                case self::ERROR_FORMAT_MESSAGES:
                    $errorList = $this->getMessageBag()->all();
                    break;
                default:
                    throw new Exception("Unknown error format: {$this->errorFormat}");
            }

            $result = $this->makeResponse($errorList);

            return $this->responseFactory->json($result, $this->errorResponseStatus);
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
