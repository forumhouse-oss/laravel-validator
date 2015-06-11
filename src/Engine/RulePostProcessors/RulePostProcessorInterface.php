<?php

namespace FHTeam\LaravelValidator\Engine\RulePostProcessors;

interface RulePostProcessorInterface
{
    /**
     * @param array $rules
     *
     * @return callable
     */
    public function postProcessRules(array &$rules);
}
