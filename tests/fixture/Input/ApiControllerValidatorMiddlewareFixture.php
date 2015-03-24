<?php

namespace FHTeam\LaravelValidator\Test\Fixture\Input;


use FHTeam\LaravelValidator\Validator\Input\RoutingMiddleware\ApiControllerValidatorMiddleware;

class ApiControllerValidatorMiddlewareFixture extends ApiControllerValidatorMiddleware
{
    protected $rules = [
        'group' => [
            'int' => 'required|numeric|min:1|max:10',
        ],
    ];
}
