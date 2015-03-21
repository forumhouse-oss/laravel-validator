<?php

namespace FHTeam\LaravelValidator\Input;

use Exception;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;

/**
 * Class AbstractRedirectingInputValidator
 *
 * @package FHTeam\LaravelValidator\Input
 */
class AbstractRedirectingInputValidator extends AbstractInputValidator
{
    /**
     * @var Redirector Laravel redirector instance
     */
    protected $redirector;
    /**
     * @var array Array of redirect rules on error
     * Example:
     *   protected $redirects = [
     *       'postLogin' => [
     *           'route' => 'login',
     *           'templateVar' => 'login_errors', //if empty, withErrors($validator) will be used
     *           'withInput' => true, //by default, can be omitted
     *       ],
     *       'postRegister' => [
     *            route => ['register', ['param1' => 'test',
     *                                   'param2' => ':inputParam1'
     *                                 ] - params prefixed with ":" taken from Input::get() by provided name
     *       ],
     *   ];
     */
    protected $errorRedirects = [];

    /**
     * @param Factory    $validatorFactory
     * @param Request    $request
     * @param Router     $router
     * @param Redirector $redirector
     */
    public function __construct(Factory $validatorFactory, Request $request, Router $router, Redirector $redirector)
    {
        parent::__construct($validatorFactory, $request, $router);
        $this->redirector = $redirector;
    }

    /**
     * Returns a redirect object to be used to redirect user browser on errors
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    protected function getRedirect()
    {
        $currentRouteMethod = $this->currentRouteMethod();

        if (!isset($this->errorRedirects[$currentRouteMethod])) {
            throw new Exception("There is no redirect data for controller method {$currentRouteMethod}");
        }

        $redirectData = $this->errorRedirects[$currentRouteMethod];

        if (isset($redirectData['route'])) {
            $redirect = $this->getRouteRedirect($redirectData['route']);
        } else {
            throw new Exception("No redirect data recognized. Supported: route");
        }

        if ((!isset($redirectData['withInput'])) || (isset($redirectData['withInput']) && $redirectData['withInput'])) {
            $redirect->withInput();
        }

        //Errors and variables
        if (empty($redirectData['formName'])) {
            $redirect->withErrors($this);
        } else {
            $redirect->withErrors($this, $redirectData['formName']);
        }

        if (!empty($redirectData['templateVar'])) {
            $redirect->with($redirectData['templateVar'], $this->getMessageBag());
        }

        return $redirect;
    }

    /**
     * @param $redirectData
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    protected function getRouteRedirect($redirectData)
    {
        //Determining route data
        if (is_array($redirectData)) {

            if (2 !== count($redirectData)) {
                throw new Exception('When passing route data as array, it should have exactly two values');
            }

            $routeName = $redirectData[0];
            $routeParams = $this->fillParameters($redirectData[1]);
            $redirect = $this->redirector->route($routeName, $routeParams);

            return $redirect;
        } else {
            $redirect = $this->redirector->route($redirectData);

            return $redirect;
        }
    }

    /**
     * Checks routing parameters for variable placeholders and replaces them with current input parameters
     *
     * @param array $routeParams
     *
     * @return array Processed routing parameters
     */
    protected function fillParameters(array $routeParams)
    {
        foreach ($routeParams as $key => $value) {
            if ($value[0] == ':') {
                $routeParams[$key] = $this->request->input(substr($value, 1));
            }

            if ($value[0] == '#') {
                $routeParams[$key] = $this->router->input(substr($value, 1));
            }
        }

        return $routeParams;
    }
}
