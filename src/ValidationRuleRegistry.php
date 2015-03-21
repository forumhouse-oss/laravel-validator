<?php

namespace FHTeam\LaravelValidator;

use App;
use Exception;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

/**
 * Class to deal with custom validation rules registration
 *
 * @package FHTeam\LaravelValidator
 */
class ValidationRuleRegistry
{
    /** Suffix of validation rule class names */
    const RULE_NAME_SUFFIX = '_validation_rule';

    /**
     * @var array An array of $ruleName => $ruleClassName items to enable rule instance creation
     */
    protected static $registry = [];

    /**
     * Registers specified class as a validation rule to Laravel Validator
     *
     * @param $className
     */
    public static function register($className)
    {
        $ruleName = Str::snake(class_basename($className));
        if (Str::endsWith($ruleName, self::RULE_NAME_SUFFIX)) {
            $ruleName = substr($ruleName, 0, -strlen(self::RULE_NAME_SUFFIX));
        }

        static::$registry[$ruleName] = $className;

        /** @var Factory $validator */
        $validator = Application::getInstance()->make(Factory::class);

        $validator->extend($ruleName, $className.'@validate');
        $validator->replacer($ruleName, $className.'@replace');
    }

    /**
     * Creates a rule instance by name
     *
     * @param string $ruleName The name of the validation rule like user_email_unique
     *
     * @return ValidationRuleInterface
     * @throws Exception
     */
    public static function create($ruleName)
    {
        if (!isset(static::$registry[$ruleName])) {
            throw new Exception("Rule with name '$ruleName' is not registered");
        }

        return App::make(static::$registry[$ruleName]);
    }
}
