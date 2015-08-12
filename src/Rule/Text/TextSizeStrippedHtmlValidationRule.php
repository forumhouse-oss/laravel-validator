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
class TextSizeStrippedHtmlValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    public function validate($attribute, $value, array $parameters = [])
    {
        $value = strip_tags($value);
        
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

    public function replace($message, $attribute, $rule, array $parameters = [])
    {
        $message = str_replace(':min', $parameters[0], $message);
        if (isset($parameters[1])) {
            $message = str_replace(':max', $parameters[1], $message);
        }

        return $message;
    }
}
