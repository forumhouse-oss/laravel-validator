<?php namespace FHTeam\LaravelValidator\Rule\Text;

use FHTeam\LaravelValidator\Rule\AbstractValidationRule;
use FHTeam\LaravelValidator\Rule\ValidationRuleInterface;

/**
 * @package FHTeam\LaravelValidator\Rule
 */
class AsciiValidationRule extends AbstractValidationRule implements ValidationRuleInterface
{
    public function validate($attribute, $value, array $parameters = [])
    {
        return mb_detect_encoding($value, 'ASCII', true);
    }
}
