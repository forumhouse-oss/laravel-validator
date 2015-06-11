<?php

namespace FHTeam\LaravelValidator\Validator;

use ArrayAccess;
use Exception;
use FHTeam\LaravelValidator\Engine\RuleParser\RuleParser;
use FHTeam\LaravelValidator\Engine\ValidatorFactory;
use FHTeam\LaravelValidator\Rule\ValidationRuleRegistry;
use FHTeam\LaravelValidator\Utility\Arr;
use FHTeam\LaravelValidator\Utility\ArrayDataStorage;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use IteratorAggregate;

/**
 * Abstract class containing logic common for all validators
 *
 * @package FHTeam\LaravelValidator
 * @mixin ArrayDataStorage
 */
abstract class AbstractValidator implements MessageProvider, ArrayAccess, IteratorAggregate
{
    /**
     * @var null|bool Null if validation never ran, false if failed, true if passed
     */
    protected $validationPassed = null;

    /**
     * @var Factory
     */
    protected $validatorFactory;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array Custom validation rules. Each rule must be an array [$validationClosure, $replacerClosure]
     *            suitable to be passed to Validator::extend() and Validator::replacer() respectively.
     *            $replacerClosure can be null
     */
    protected $customRules = [];

    /**
     * @var string[]
     */
    protected $customValidationMessages = [];

    /**
     * @var ArrayDataStorage
     */
    protected $dataStorage;

    /**
     * @var callable|int
     */
    protected $keyCase = ArrayDataStorage::KEY_CASE_CAMEL;

    /**
     * Template variables to replace in rules
     *
     * @var array
     */
    protected $templateReplacements = [];

    /**
     * @var array
     */
    protected $failedMessages;

    /**
     * @var array
     */
    protected $failedRules;

    /**
     * @param $object
     *
     * @return string
     */
    abstract protected function getState($object);

    /**
     * @param $object
     *
     * @return array
     */
    abstract protected function getObjectData($object = null);

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
     * Validates an object and raises exception, if it is not valid
     *
     * @param mixed $object
     *
     * @throws ValidationException
     */
    public function assertIsObjectValid($object)
    {
        if (null === $this->validationPassed) {
            $this->validationPassed = $this->isThisValid($object);
        }

        if (!$this->validationPassed) {
            throw new ValidationException("Passed object supposed to be valid, but it is not", $this);
        }
    }

    /**
     * Validates an object and raises exception, if it is not valid
     *
     * @throws ValidationException
     */
    public function assertValidationPassed()
    {
        if (!$this->validationPassed) {
            throw new ValidationException("Validation has not passed", $this);
        }
    }

    /**
     * Public validation function to validate some object
     *
     * @param mixed $object Object to validate
     *
     * @return bool
     */
    public function isThisValid($object = null)
    {
        $objectData = $this->getObjectData($object);
        $validationGroup = $this->getState($object);
        $rules = Arr::mergeByCondition($this->rules, $validationGroup);

        $ruleParser = new RuleParser();
        $rules = $ruleParser->parse($rules, $this->templateReplacements);
        $rules = $this->preProcessRules($rules, $objectData);

        //Registering custom validation rules
        foreach ($this->customRules as $ruleName => $customRuleData) {
            ValidationRuleRegistry::registerClosure($customRuleData[0], $customRuleData[1]);
        }

        //Creating validator
        $validatorFactory = new ValidatorFactory($this->validatorFactory);
        $validator = $validatorFactory->create($rules, $objectData);
        $validator->setCustomMessages(
            $this->preProcessValidationErrorMessages($this->customValidationMessages, $rules, $objectData)
        );

        $this->setupValidator($validator);
        $method_name = 'setupValidatorFor'.Str::studly($validationGroup);

        if (method_exists($this, $method_name)) {
            $this->$method_name($validator);
        }

        $this->validationPassed = !$validator->fails();

        if ($this->validationPassed) {
            $this->dataStorage = new ArrayDataStorage($this->keyCase);
            $this->dataStorage->setItems(Arr::only($objectData, array_keys($rules)));
            $this->failedMessages = new MessageBag();
            $this->failedRules = [];
        } else {
            $this->failedMessages = $validator->getMessageBag();
            $this->failedRules = $validator->failed();
            $this->dataStorage = null;
        }

        return $this->validationPassed;
    }

    /**
     * Adds template variables to process with rules
     *
     * @param array $vars
     *
     * @throws Exception
     * @return $this
     */
    public function addTemplateReplacements(array $vars)
    {
        foreach ($vars as $varName => $varValue) {
            $this->templateReplacements['{'.$varName.'}'] = $varValue;
        }

        return $this;
    }

    /**
     * Manually sets validation rules. This method may be called at any time before isThisValid()
     *
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Method is called to preprocess rules if required.
     *
     * @param array $rules Rules to preprocess
     * @param array $data  Data being validated
     *
     * @return array Preprocessed rules
     */
    public function preProcessRules(array $rules, array $data)
    {
        return $rules;
    }

    /**
     * Method is called to preprocess validation error messages if required
     *
     * @param string[] $messages
     * @param array    $rules
     * @param array    $data
     *
     * @return string[]
     */
    public function preProcessValidationErrorMessages(array $messages, array $rules, array $data)
    {
        return $messages;
    }

    /**
     * Method is called to prepare validator for validation
     * just before passed() is called
     *
     * @param Validator $validator
     */
    public function setupValidator(Validator $validator)
    {
    }

    public function isValidationPassed()
    {
        return $this->validationPassed;
    }

    /**
     * Returns text version about what failed
     *
     * @return MessageBag
     */
    public function getMessageBag()
    {
        return $this->failedMessages;
    }

    /**
     * Returns a list of failed rules
     *
     * @return array
     */
    public function getFailedRules()
    {
        return $this->failedRules;
    }

    public function getIterator()
    {
        $this->assertValidationPassed();

        return $this->dataStorage->getIterator();
    }

    public function offsetExists($offset)
    {
        $this->assertValidationPassed();

        return $this->dataStorage->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        $this->assertValidationPassed();

        //TODO: apply converter
        return $this->dataStorage->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->assertValidationPassed();
        $this->dataStorage->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->assertValidationPassed();
        $this->dataStorage->offsetUnset($offset);
    }

    public function __isset($name)
    {
        $this->assertValidationPassed();

        return $this->dataStorage->__isset($name);
    }

    public function __unset($name)
    {
        $this->assertValidationPassed();
        $this->dataStorage->__unset($name);
    }

    public function __get($name)
    {
        $this->assertValidationPassed();

        //TODO: apply converter
        return $this->dataStorage->__get($name);
    }

    public function __set($name, $value)
    {
        $this->assertValidationPassed();
        $this->dataStorage->__set($name, $value);
    }

    public function __invoke($object)
    {
        return $this->isThisValid($object);
    }

    public function __toString()
    {
        return implode("\r\n", $this->failedMessages);
    }

    /**
     * Enabling access to getItemOrDefault() via magic methods
     *
     * @param string $name
     * @param array  $params
     *
     * @return mixed
     * @throws Exception
     */
    public function __call($name, array $params)
    {
        $this->assertValidationPassed();

        if (method_exists($this->dataStorage, $name)) {
            return call_user_func_array([$this->dataStorage, $name], $params);
        }

        if (count($params) !== 1) {
            throw new Exception(
                "You must pass exactly one argument for validator as a default value. You passed: ".json_encode(
                    $params
                )
            );
        }

        return $this->dataStorage->getItemOrDefault($name, $params[0]);
    }

    public function __debugInfo()
    {
        return [
            "validationPassed" => $this->validationPassed,
            "rules" => $this->rules,
            "dataStorage::getItems()" => $this->dataStorage ? $this->dataStorage->getItems() : '(null)',
            "keyCase" => $this->keyCase,
            "templateReplacements" => $this->templateReplacements,
            "failedMessages" => $this->failedMessages,
            "failedRules" => $this->failedRules,
        ];
    }
}
