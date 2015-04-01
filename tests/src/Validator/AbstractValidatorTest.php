<?php

namespace FHTeam\LaravelValidator\Test\Validator;

use Exception;
use FHTeam\LaravelValidator\Test\Fixture\AbstractValidatorFixture;
use FHTeam\LaravelValidator\Test\TestBase;
use FHTeam\LaravelValidator\Validator\ValidationException;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\MessageBag;

/**
 * Class AbstractValidatorTest
 *
 * @package FHTeam\LaravelValidator\Test
 */
class AbstractValidatorTest extends TestBase
{
    /**
     * @var AbstractValidatorFixture
     */
    protected $validator;

    /**
     * @var array Valid data (passes validation)
     */
    protected $valid = [
        'string' => 'string',
        'int' => 10,
    ];

    /**
     * @var array Invalid data (should fail validation)
     */
    protected $invalid = [
        'string1' => 'string',
        'int' => 'askjaksjakjskasj',
    ];

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->validator = new AbstractValidatorFixture(
            Container::getInstance()->make(Factory::class)
        );
    }

    /**
     * @throws Exception
     */
    public function testValidate()
    {
        $this->assertNull($this->validator->isValidationPassed());
        $this->assertTrue($this->validator->isThisValid($this->valid));
        $this->assertTrue($this->validator->isValidationPassed());
    }

    /**
     * @throws Exception
     */
    public function testGetValuesSuccess()
    {
        $this->validator->isThisValid($this->valid);
        $this->assertEquals('string', $this->validator->getItem('string'));
        $this->assertEquals([], $this->validator->getFailedRules());
        $this->assertEquals(new MessageBag(), $this->validator->getMessageBag());
    }

    /**
     * @throws Exception
     */
    public function testGetValueDefault()
    {
        $this->validator->isThisValid($this->valid);
        $this->assertEquals('notExists', $this->validator->getItemOrDefault('notExistsKey', 'notExists'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals('notExists', $this->validator->notExistsKey('notExists'));
    }

    /**
     * @throws Exception
     */
    public function testGetValuesOnUnvalidated()
    {
        $this->setExpectedException(ValidationException::class);
        $this->validator->isThisValid($this->invalid);
        $this->validator->getItem('string');
    }

    /**
     * @throws Exception
     */
    public function testDataContainsOnlyValidatedValues()
    {
        $this->validator->isThisValid($this->valid + ['should_not_be_there' => 'value']);
        $this->assertEquals($this->valid, $this->validator->getItems());
    }

    /**
     * @throws Exception
     */
    public function testTemplateReplacements()
    {
        $this->validator->setRules(['group' => ['testtpl' => 'required|min:{min}|max:{max}|numeric']]);

        $this->validator->addTemplateReplacements(['min' => 1, 'max' => 10]);
        $this->assertTrue($this->validator->isThisValid(['testtpl' => 5]), 'Valid');

        $this->assertFalse($this->validator->isThisValid(['testtpl' => 100]), 'Invalid too big');
        $this->assertFalse($this->validator->isThisValid(['testtpl' => -100]), 'Invalid, too small');
    }

    public function testSetupValidatorExecuted()
    {
        $this->validator->setRules(['group' => ['dummy' => '']]);
        $this->assertTrue($this->validator->isThisValid(['sometimes' => 1]));
        $this->assertFalse($this->validator->isThisValid(['sometimes' => 2]));
    }
}
