<?php

namespace FHTeam\LaravelValidator\Engine\RuleParser;

/**
 * Class RuleParser
 *
 * @package FHTeam\LaravelValidator\Engine\RuleParser
 */
class RuleParser
{
    /**
     * @param array $rules
     * @param array $templateReplacements
     *
     * @return array
     */
    public function parse(array $rules, array $templateReplacements)
    {
        $rules = $this->explodeRules($rules);
        $rules = $this->makeRuleTemplateReplacements($rules, $templateReplacements);

        return $rules;
    }

    /**
     * Explode the rules into an array of rules.
     *
     * @param  string|array $rules
     *
     * @return array
     */
    protected function explodeRules($rules)
    {
        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }

        return $rules;
    }

    /**
     * @param array $rules
     *
     * @return array
     */
    protected function makeRuleTemplateReplacements(array $rules, $templateReplacements)
    {
        // Making template replacements
        foreach ($rules as $attribute => &$ruleData) {
            foreach ($ruleData as &$parameters) {
                $parameters = str_replace(
                    array_keys($templateReplacements),
                    array_values($templateReplacements),
                    $parameters
                );
            }
        }

        return $rules;
    }
}
