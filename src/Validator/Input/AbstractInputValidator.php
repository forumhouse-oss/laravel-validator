<?php

namespace FHTeam\LaravelValidator\Validator\Input;

use ArrayAccess;
use FHTeam\LaravelValidator\Validator\AbstractValidator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use IteratorAggregate;

/**
 * Class to validate input in the controllers
 *
 * @package FHTeam\LaravelValidatorValidator
 */
class AbstractInputValidator extends AbstractValidator implements ArrayAccess, IteratorAggregate
{
    /**
     * If we need to include input data ($_GET and $_POST arrays) into data for validation
     */
    const VALIDATE_INPUT = 1;

    /**
     * If we need to include header data ($_GET and $_POST arrays) into data for validation
     */
    const VALIDATE_HEADERS = 2;

    /**
     * If we need to include raw cookies ($_COOKIE) into data for validation
     */
    const VALIDATE_RAW_COOKIES = 4;

    /**
     * If we need to include encrypted Laravel cookies ($_COOKIE) into data for validation
     */
    const VALIDATE_LARAVEL_COOKIES = 8;

    /**
     * If we need to include Laravel session data into data for validation
     */
    const VALIDATE_RAW_SESSION = 16;

    /**
     * If we need to include Laravel session data into data for validation
     */
    const VALIDATE_LARAVEL_SESSION = 32;

    /**
     * A bit mask of data to validate
     *
     * @var int
     */
    protected $inputTypes = self::VALIDATE_INPUT;

    /**
     * @var Request Current HTTP request to fetch data from
     */
    protected $request;

    /**
     * @var Router Laravel router instance
     */
    protected $router;

    /**
     * @param Factory $validatorFactory
     * @param Request $request
     * @param Router  $router
     */
    public function __construct(Factory $validatorFactory, Request $request, Router $router)
    {
        parent::__construct($validatorFactory);
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * @param int $inputTypes
     */
    public function setInputTypes($inputTypes)
    {
        $this->inputTypes = $inputTypes;
    }

    /**
     * @param $object
     *
     * @return string
     */
    protected function getValidationGroup($object)
    {
        return $this->currentRouteMethod();
    }

    /**
     * @param mixed $object
     *
     * @return array
     */
    protected function getObjectData($object = null)
    {
        return $this->collectData();
    }


    /**
     * Returns the current method of current controller
     *
     * @return mixed
     */
    protected function currentRouteMethod()
    {
        return explode('@', $this->router->currentRouteAction())[1];
    }

    /**
     * @return array
     */
    public function collectData()
    {
        $data = [];

        if ($this->inputTypes & self::VALIDATE_INPUT) {
            $data = array_merge($data, $this->request->all());
        }

        if ($this->inputTypes & self::VALIDATE_HEADERS) {
            $data = array_merge($data, $this->request->header());
        }

        if ($this->inputTypes & self::VALIDATE_RAW_COOKIES) {
            $data = array_merge($data, $_COOKIE);
        }

        if ($this->inputTypes & self::VALIDATE_LARAVEL_COOKIES) {
            $data = array_merge($data, $this->request->cookie());
        }

        if ($this->inputTypes & self::VALIDATE_RAW_SESSION) {
            $data = array_merge($data, $_SESSION);
        }

        if ($this->inputTypes & self::VALIDATE_LARAVEL_SESSION) {
            $data = array_merge($data, $this->request->session()->all());
        }

        return $data;
    }
}
