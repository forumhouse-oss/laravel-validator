<?php namespace FHTeam\LaravelValidator\Rule\Json;

use FHTeam\LaravelValidator\Rule\AbstractValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;

/**
 * Validation rule to test if a given string is not empty (does not consist of spaces, for instance)
 *
 * @package FHTeam\LaravelValidator\Rule
 */
class HasTextValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    public function validate($attribute, $value, array $parameters = [])
    {
        return mb_strlen(trim($value)) > 0;
    }
}
