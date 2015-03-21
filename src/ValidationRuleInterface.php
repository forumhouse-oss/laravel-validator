<?php namespace FHTeam\LaravelValidator;

/**
 * Interface to be implemented by all custom validation rules
 *
 * @package FHTeam\LaravelValidator
 */
interface ValidationRuleInterface
{
    /**
     * @param string $attribute
     * @param mixed  $value
     * @param array  $parameters
     *
     * @return bool
     */
    public function validate($attribute, $value, array $parameters = []);

    /**
     * @param string $message
     * @param string $attribute
     * @param string $rule
     * @param array  $parameters
     *
     * @return void
     */
    public function replace($message, $attribute, $rule, array $parameters = []);
}
