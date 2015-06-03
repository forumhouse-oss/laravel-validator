<?php

namespace FHTeam\LaravelValidator\Tests\Fixture;

use FHTeam\LaravelValidator\Validator\AbstractValidator;
use Illuminate\Validation\Validator;

/**
 * Class AbstractValidatorConcrete
 *
 * @package FHTeam\LaravelValidator\Test\Fixture
 */
class AbstractValidatorFixture extends AbstractValidator
{
    protected $rules = [
        'unused' => ['dummy' => 'required'],
        'group' => [
            'string' => 'required',
            'int' => 'required|numeric',
        ],
        'other_group' => ['dummy']
    ];

    protected $group = 'group';

    /**
     * @param $object
     *
     * @return string
     */
    protected function getState($object)
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
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

    public function setupValidator(Validator $validator)
    {
        parent::setupValidator($validator);
        $validator->sometimes(
            'sometimes',
            'numeric|min:1|max:1',
            function () {
                return true;
            }
        );
    }

    public function setupValidatorForOtherGroup(Validator $validator)
    {
        parent::setupValidator($validator);
        $validator->sometimes(
            'sometimes_other',
            'numeric|min:100|max:100',
            function () {
                return true;
            }
        );
    }
}
