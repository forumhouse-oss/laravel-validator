<?php

namespace FHTeam\LaravelValidator\Test\Fixture;

use FHTeam\LaravelValidator\Utility\ArrayDataStorage;
use FHTeam\LaravelValidator\Validator\Input\AbstractInputValidator;

/**
 * Class AbstractValidatorConcrete
 *
 * @mixin ArrayDataStorage
 * @package FHTeam\LaravelValidator\Test\Fixture
 */
class AbstractInputValidatorFixture extends AbstractInputValidator
{
    protected $rules = [
        'unused' => ['dummy' => 'required'],
        'group' => [
            'string' => 'required',
            'int' => 'required|numeric',
        ],
    ];

    protected $errorRedirects = [
        'simple_route' => [
            'route' => 'test_route',
        ],
        'simple_route_array' => [
            'route' => ['test_route', ['param1' => 'value1']],
        ],
        'simple_action' => [
            'action' => 'test_route',
        ]
    ];

    /**
     * @var string
     */
    protected $currentRouteMethod;

    /**
     * @param $object
     *
     * @return string
     */
    protected function getValidationGroup($object)
    {
        return 'group';
    }

    /**
     * @param $object
     *
     * @return array
     */
    protected function getObjectData($object = null)
    {
        return $object;
    }

    protected function currentRouteMethod()
    {
        $this->currentRouteMethod;
    }

    /**
     * @param string $currentRouteMethod
     */
    public function setCurrentRouteMethod($currentRouteMethod)
    {
        $this->currentRouteMethod = $currentRouteMethod;
    }
}
