<?php namespace FHTeam\LaravelValidator\Test\Fixture\Input;

use FHTeam\LaravelValidator\Input\RoutingMiddleware\FrontendControllerValidatorMiddleware;

class FrontendControllerValidatorMiddlewareFixture extends FrontendControllerValidatorMiddleware
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
