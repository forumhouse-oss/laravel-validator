<?php namespace FHTeam\LaravelValidator\Rule\Text;

use Exception;
use FHTeam\LaravelValidator\Rule\AbstractValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;

/**
 * Validation rule to test given string's size using Unicode text functions
 * Minimum and maximum valid sizes for array can be specified by first and second optional parameters
 *
 * @package FHTeam\LaravelValidator\Rule
 */
class TextSizeValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    public function validate($attribute, $value, array $parameters = [])
    {
        if (count($parameters) < 1) {
            throw new Exception("Text Size validation rule requires at least one parameter");
        }

        if (mb_strlen($value) < $parameters[0]) {
            return false;
        }

        if (isset($parameters[1]) && mb_strlen($value) > $parameters[1]) {
            return false;
        }

        return true;
    }
}
