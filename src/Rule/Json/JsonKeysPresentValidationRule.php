<?php namespace FHTeam\LaravelValidator\Rule\Json;

use FHTeam\LaravelValidator\Rule\AbstractValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;
use FHTeam\LaravelValidator\Utility\Arr;

/**
 * Validation rule to test if given keys are present in a passed JSON string. Nested values are supported. Just
 * separate parent and child with dots ('parent1.child1, parent2.child2')
 *
 * @package FHTeam\LaravelValidator\Rule
 */
class JsonKeysPresentValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    public function validate($attribute, $value, array $parameters = [])
    {
        $result = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        foreach ($parameters as $parameter) {
            if (null === Arr::get($result, $parameter)) {
                return false;
            }
        }
        return true;
    }
}
