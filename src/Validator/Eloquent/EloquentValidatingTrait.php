<?php namespace FHTeam\LaravelValidator\Validator\Eloquent;

use Exception;
use FHTeam\LaravelValidator\Validator\AbstractValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;

/**
 * Using this trait from the model turns it into validating one
 *
 * @package Eloquent
 */
trait EloquentValidatingTrait
{
    /**
     * @var bool If this model should be automatically validated on save. Exception will be thrown on any error
     */
    public static $validateBeforeSaving = true;

    /**
     * @var string Returns a class name suffix for a model validator. By default validator for model Acme\MyModel is
     *      Acme\MyModelValidator
     */
    public static $validatorClassNameSuffix = 'Validator';

    /**
     * Eloquent will call this on model boot
     */
    public static function bootEloquentValidatingTrait()
    {
        // Calling Model::saving() and asking it to execute assertIsValid() before model is saved into database
        forward_static_call(
            [static::class, 'saving'],
            [static::class, 'assertIsValid']
        );
    }

    public function validate()
    {
        $validator = static::createValidator();

        return $validator->isThisValid($this);
    }

    public static function assertIsValid(Model $model)
    {
        if (!static::$validateBeforeSaving) {
            return;
        }

        $validator = static::createValidator();
        $validator->assertIsObjectValid($model);
    }

    /**
     * @return AbstractValidator
     * @throws Exception
     */
    protected static function createValidator()
    {
        $modelClass = static::class;
        $className = $modelClass.static::$validatorClassNameSuffix;

        if (!class_exists($className)) {
            throw new Exception("Cannot load validator class '$className' for model '$modelClass'");
        }

        return Application::getInstance()->make($className);
    }
}
