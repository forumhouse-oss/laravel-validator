<?php

namespace FHTeam\LaravelValidator\Engine\RulePostProcessors;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

/**
 * Class ArrayRulePostProcessor
 *
 * @package FHTeam\LaravelValidator\Engine\RulePostProcessors
 */
class ArrayRulePostProcessor implements RulePostProcessorInterface
{
    /**
     * @param array $rules
     *
     * @return callable[]
     * @throws Exception
     */
    public function postProcessRules(array &$rules)
    {
        $postProcessors = [];
        $hasArrayRule = false;
        foreach ($rules as $attribute => &$attributeRules) {
            foreach ($attributeRules as $ruleIndex => &$ruleData) {
                [$ruleName, $ruleParams] = $this->parseRule($ruleData);

                if ('array' == $ruleName) {
                    $hasArrayRule = true;
                }

                if (!Str::endsWith($ruleName, '[]')) {
                    continue;
                }

                $ruleName = substr($ruleName, 0, -2);
                if (Str::endsWith($ruleName, '[]')) {
                    throw new Exception(
                        "Error in rule '$ruleName' for attribute '$attribute'. Multidimensional arrays are not currently supported"
                    );
                }

                if ($hasArrayRule) {
                    unset($attributeRules[$ruleIndex]);
                } else {
                    $ruleData = ['array'];
                }

                $postProcessors[] = function (Validator $validator) use ($attribute, $ruleName, $ruleParams) {
                    $validator->addRules([$attribute.'.*' => [$ruleName.':'.implode(', ', $ruleParams)]]);
                };
            }
        }

        return $postProcessors;
    }

    /**
     * Extract the rule name and parameters from a rule.
     *
     * @param  array|string $rules
     *
     * @return array
     */
    protected function parseRule($rules)
    {
        if (is_array($rules)) {
            return $this->parseArrayRule($rules);
        }

        return $this->parseStringRule($rules);
    }

    /**
     * Parse an array based rule.
     *
     * @param  array $rules
     *
     * @return array
     */
    protected function parseArrayRule(array $rules)
    {
        return [Str::studly(trim(Arr::get($rules, 0))), array_slice($rules, 1)];
    }

    /**
     * Parse a string based rule.
     *
     * @param  string $rules
     *
     * @return array
     */
    protected function parseStringRule($rules)
    {
        $parameters = [];

        // The format for specifying validation rules and parameters follows an
        // easy {rule}:{parameters} formatting convention. For instance the
        // rule "Max:3" states that the value may only be three letters.
        if (strpos($rules, ':') !== false) {
            [$rules, $parameter] = explode(':', $rules, 2);

            $parameters = $this->parseParameters($rules, $parameter);
        }

        return [Str::studly(trim($rules)), $parameters];
    }

    /**
     * Parse a parameter list.
     *
     * @param  string $rule
     * @param  string $parameter
     *
     * @return array
     */
    protected function parseParameters($rule, $parameter)
    {
        if (strtolower($rule) == 'regex') {
            return [$parameter];
        }

        return str_getcsv($parameter);
    }
}
