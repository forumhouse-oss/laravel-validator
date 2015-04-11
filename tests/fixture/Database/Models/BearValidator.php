<?php

namespace FHTeam\LaravelValidator\Tests\Fixture\Database\Models;

use FHTeam\LaravelValidator\Validator\Eloquent\EloquentModelValidator;

class BearValidator extends EloquentModelValidator
{
    protected $rules = [
        'name' => 'required',
        'type' => 'required|in:Grizzly,Black,Polar',
        'danger_level' => 'required|numeric',
    ];
}
