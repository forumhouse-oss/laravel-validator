<?php namespace FHTeam\LaravelValidator\Rule;

use FHTeam\LaravelValidator\ValidationRuleInterface;

/**
 * Class AbstractValidationRule
 *
 * @package FHTeam\LaravelValidator
 */
abstract class AbstractValidationRule implements ValidationRuleInterface
{
    public function replace($message, $attribute, $rule, array $parameters = [])
    {
        return $message;
    }
}
