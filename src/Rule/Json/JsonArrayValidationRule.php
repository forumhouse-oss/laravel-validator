<?php namespace FHTeam\LaravelValidator\Rule\Json;

use FHTeam\LaravelValidator\Rule\AbstractValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;

/**
 * Validation rule to test if a given JSON string contains array.
 * Minimum and maximum valid sizes for array can be specified by first and second optional parameters
 *
 * @package FHTeam\LaravelValidator\Rule
 */
class JsonArrayValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    public function validate($attribute, $value, array $parameters = [])
    {
        $result = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        if (!is_array($result)) {
            return false;
        }

        $arrayLength = count($result);

        if (isset($parameters[0]) && $arrayLength < $parameters[0]) {
            return false;
        }

        if (isset($parameters[1]) && $arrayLength > $parameters[1]) {
            return false;
        }

        return true;
    }
}
