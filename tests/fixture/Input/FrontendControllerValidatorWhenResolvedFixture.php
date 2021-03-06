<?php namespace FHTeam\LaravelValidator\Tests\Fixture\Input;

use FHTeam\LaravelValidator\Validator\Input\WhenResolved\FrontendControllerValidatorWhenResolved;

/**
 * Class FrontendControllerValidatorMiddlewareFixture
 *
 * @package FHTeam\LaravelValidator\Test\Fixture\Input
 */
class FrontendControllerValidatorWhenResolvedFixture extends FrontendControllerValidatorWhenResolved
{
    protected $rules = [
        'group' => [
            'int' => 'required|numeric|min:1|max:10',
        ],
    ];

    protected $errorRedirects = [
        'group' => [
            'route' => 'test_route',
        ]
    ];
}
