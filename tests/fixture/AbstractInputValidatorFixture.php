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

    /**
     * @param $object
     *
     * @return array
     */
    protected function getObjectData($object = null)
    {
        return $object;
    }
}