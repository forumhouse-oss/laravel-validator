<?php namespace FHTeam\LaravelValidator\Rule\Json;

use FHTeam\LaravelValidator\Rule\AbstractValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;

/**
 * Validates passed value as JSON

 *
*@package FHTeam\LaravelValidator\Rule
 */
class JsonValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    public function validate($attribute, $value, array $parameters = [])
    {
        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
