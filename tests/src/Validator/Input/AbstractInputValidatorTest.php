<?php

namespace FHTeam\LaravelValidator\Tests\Validator\Input;

use FHTeam\LaravelValidator\Validator\Input\AbstractInputValidator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Factory;

/**
 * Class AbstractInputValidatorTest
 *
 * @package FHTeam\LaravelValidator\Test\Input
 */
class AbstractInputValidatorTest extends InputValidatorTestBase
{
    protected $dataInput = ['inputKey1' => 'inputValue1', 'inputKey2' => 'inputValue2'];

    protected $dataHeader = ['headerKey1' => 'headerValue1', 'headerKey2' => 'headerValue2'];

    public function setUp(): void
    {
        parent::setUp();

        $this->setRequestData($this->dataInput);
        $this->setHeaderData($this->dataHeader);
    }


    public function testCollectDataInput()
    {
        $validator = $this->createValidator();
        $validator->setInputTypes(AbstractInputValidator::VALIDATE_INPUT);
        $this->assertEquals($this->dataInput, $validator->collectData());
    }

    public function testCollectDataInputAndHeader()
    {
        $validator = $this->createValidator();
        $validator->setInputTypes(
            AbstractInputValidator::VALIDATE_INPUT | AbstractInputValidator::VALIDATE_HEADERS
        );
        $this->assertEquals($this->dataInput + $this->dataHeader, $validator->collectData());
    }

    /**
     * @return AbstractInputValidator
     */
    protected function createValidator()
    {
        return new AbstractInputValidator(
            Container::getInstance()->make(Factory::class),
            $this->request,
            $this->router
        );
    }
}
