<?php

namespace FHTeam\LaravelValidator\Rule\Arrays;

use FHTeam\LaravelValidator\Rule\AbstractValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;

/**
 * @package FHTeam\LaravelValidator
 */
class ArrayOfUIntValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    /**
     * @param string $attribute
     * @param mixed  $value
     * @param array  $parameters
     *
     * @return bool
     */
    public function validate($attribute, $value, array $parameters = [])
    {
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (!ctype_digit($item)) {
                return false;
            }

            if ($item > PHP_INT_MAX) {
                return false;
            }
        }

        return true;
    }
}