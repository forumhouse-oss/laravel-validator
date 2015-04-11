<?php

namespace FHTeam\LaravelValidator\Tests\Fixture\Input;


use FHTeam\LaravelValidator\Validator\Input\WhenResolved\ApiControllerValidatorWhenResolved;

class ApiControllerValidatorWhenResolvedFixture extends ApiControllerValidatorWhenResolved
{
    protected $rules = [
        'group' => [
            'int' => 'required|numeric|min:1|max:10',
        ],
    ];
}
