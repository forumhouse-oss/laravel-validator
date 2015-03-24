<?php

namespace FHTeam\LaravelValidator\Test\Fixture\Input;


use FHTeam\LaravelValidator\Input\ValidatesWhenResolved\ApiControllerValidatorWhenResolved;

class ApiControllerValidatorWhenResolvedFixture extends ApiControllerValidatorWhenResolved
{
    protected $rules = [
        'group' => [
            'int' => 'required|numeric|min:1|max:10',
        ],
    ];
}
