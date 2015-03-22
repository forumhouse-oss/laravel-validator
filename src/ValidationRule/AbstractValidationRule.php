<?php namespace FHTeam\LaravelValidator\ValidationRule;

use FHTeam\LaravelValidator\ValidationRuleInterface;

/**
 * Class AbstractValidationRule
 *
 * @package FHTeam\LaravelValidator
 */
abstract class AbstractValidationRule implements ValidationRuleInterface
{
    /**
     * Empty replace rule to ease rule implementation
     *
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return string
     */
    public function replace($message, $attribute, $rule, array $parameters = [])
    {
        return $message;
    }
}
