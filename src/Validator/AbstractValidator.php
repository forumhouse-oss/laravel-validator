<?php

namespace FHTeam\LaravelValidator\Validator;

use ArrayAccess;
use BadMethodCallException;
use Exception;
use FHTeam\LaravelValidator\Utility\Arr;
use FHTeam\LaravelValidator\Utility\ArrayDataStorage;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\MessageBag;
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
    abstract protected function getValidationGroup($object);

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
     * Internal validation function to validate already serialized data.
     *
     * @param mixed $object Object to validate
     *
     * @return bool
     */
    public function isThisValid($object = null)
    {
        $objectData = $this->getObjectData($object);
        $validationGroup = $this->getValidationGroup($object);
        $rules = Arr::mergeByCondition($this->rules, $validationGroup);
        $rules = $this->preProcessRules($rules, $objectData);

        $validator = $this->validatorFactory->make($objectData, $rules);
        $this->setupValidator($validator);
        $this->validationPassed = !$validator->fails();

        if ($this->validationPassed) {
            $this->dataStorage = new ArrayDataStorage($this->keyCase);
            $this->dataStorage->setItems($objectData);
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
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Method is called to preprocess rules if required. By default it templatize them
     *
     * @param array $rules Rules to preprocess
     * @param array $data  Data to be validated
     *
     * @return array Preprocessed rules
     */
    public function preProcessRules(array $rules, array $data)
    {
        // Making template replacements
        foreach ($rules as $key => $text) {
            if (empty($text)) {
                continue;
            }

            $rulesArray = explode(
                '|',
                str_replace(
                    array_keys($this->templateReplacements),
                    array_values($this->templateReplacements),
                    $text
                )
            );

            foreach ($rulesArray as $ruleData) {
                if ('#' !== $ruleData[0]) {
                    continue;
                    //TODO: extract converters and unset them
                }
            }

            $rules[$key] = $rulesArray;
        }

        return $rules;
    }

    /**
     * Method is called to prepare validator for validation
     * just before passed() is called
     *
     * @param Validator $validator
     */
    public function setupValid\FHTeam\LaravelValidator\Validator\AbstractValidator::setupValidatorator(Validator $validator)
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

    public function __call($name, $params)
    {
        $this->assertValidationPassed();

        if (!method_exists($this->dataStorage, $name)) {
            throw new BadMethodCallException("There is no method '$name' on validator or its data storage");
        }

        return call_user_func_array([$this->dataStorage, $name], $params);
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
