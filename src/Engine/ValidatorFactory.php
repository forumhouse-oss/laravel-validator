<?php

namespace FHTeam\LaravelValidator\Engine;

use FHTeam\LaravelValidator\Engine\RulePostProcessors\ArrayRulePostProcessor;
use FHTeam\LaravelValidator\Engine\RulePostProcessors\RulePostProcessorInterface;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\Validator;

/**
 * Class ValidatorCreator
 *
 * @package FHTeam\LaravelValidator\Engine
 */
class ValidatorFactory
{
    /**
     * IoC invoked constructor
     *
     * @param Factory $validatorFactory
     */
    public function __construct(Factory $validatorFactory)
    {
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @param array $rules
     * @param array $objectData
     *
     * @return Validator
     */
    public function create(array $rules, array $objectData)
    {
        /** @var Validator $validator */
        $validator = $this->validatorFactory->make([], []);

        $validatorCallables = [];
        $rulePostProcessors = [];
        $rulePostProcessors[] = new ArrayRulePostProcessor();

        foreach ($rulePostProcessors as $processor) {
            /** @var RulePostProcessorInterface $processor */
            $validatorCallables = array_merge($validatorCallables, $processor->postProcessRules($rules));
        }

        $validator->setRules($rules);
        $validator->setData($objectData);
        foreach ($validatorCallables as $callable) {
            call_user_func($callable, $validator);
        }

        return $validator;
    }
}
