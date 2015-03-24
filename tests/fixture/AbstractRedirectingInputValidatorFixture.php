<?php

namespace FHTeam\LaravelValidator\Test\Fixture;

use FHTeam\LaravelValidator\Utility\ArrayDataStorage;
use FHTeam\LaravelValidator\Validator\Input\AbstractRedirectingInputValidator;

/**
 * Class AbstractValidatorConcrete
 *
 * @mixin ArrayDataStorage
 * @package FHTeam\LaravelValidator\Test\Fixture
 */
class AbstractRedirectingInputValidatorFixture extends AbstractRedirectingInputValidator
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
}
