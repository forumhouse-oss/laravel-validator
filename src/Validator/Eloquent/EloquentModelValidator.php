<?php namespace FHTeam\LaravelValidator\Validator\Eloquent;

use FHTeam\LaravelValidator\Validator\AbstractValidator;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentModelValidator
 *
 * @package FHTeam\LaravelValidator\Eloquent
 */
abstract class EloquentModelValidator extends AbstractValidator
{
    /**
     * @param Model $object
     *
     * @return array
     */
    protected function getObjectData($object = null)
    {
        return $object->getAttributes();
    }

    /**
     * @param Model $object
     *
     * @return null
     */
    protected function getValidationGroup($object)
    {
        return null; //No model state by default
    }
}
