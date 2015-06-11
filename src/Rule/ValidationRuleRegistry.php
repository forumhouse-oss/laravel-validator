<?php

namespace FHTeam\LaravelValidator\Rule;

use Closure;
use Exception;
use Illuminate\Container\Container;
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

        /** @var Factory $validatorFactory */
        $validatorFactory = Application::getInstance()->make(Factory::class);

        $validatorFactory->extend($ruleName, $className.'@validate');
        $validatorFactory->replacer($ruleName, $className.'@replace');
    }

    /**
     * Registers provided closure as a validation rule. Useful for quick & dirty validation
     *
     * @param string              $ruleName
     * @param string|Closure      $validateFunction
     * @param string|Closure|null $replaceFunction
     */
    public static function registerClosure($ruleName, $validateFunction, $replaceFunction = null)
    {
        /** @var Factory $validatorFactory */
        $validatorFactory = Application::getInstance()->make(Factory::class);

        $validatorFactory->extend($ruleName, $validateFunction);
        if (null !== $replaceFunction) {
            $validatorFactory->replacer($ruleName, $replaceFunction);
        }
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

        return Container::getInstance()->make(static::$registry[$ruleName]);
    }
}
