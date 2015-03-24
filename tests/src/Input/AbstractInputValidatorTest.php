<?php

namespace FHTeam\LaravelValidator\Test\Input;

use FHTeam\LaravelValidator\Test\TestBase;
use FHTeam\LaravelValidator\Validator\Input\AbstractInputValidator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class AbstractInputValidatorTest
 *
 * @package FHTeam\LaravelValidator\Test\Input
 */
class AbstractInputValidatorTest extends TestBase
{
    protected $dataInput = ['inputKey1' => 'inputValue1', 'inputKey2' => 'inputValue2'];

    protected $dataHeader = ['headerKey1' => 'headerValue1', 'headerKey2' => 'headerValue2'];

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Request
     */
    protected $request;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Router
     */
    protected $router;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->request = $this->getMockBuilder(Request::class)->getMock();
        $this->request->expects($this->any())->method('all')->willReturn($this->dataInput);
        $this->request->expects($this->any())->method('header')->willReturn($this->dataHeader);
        $this->router = $this->getMockBuilder(Router::class)->disableOriginalConstructor()->getMock();
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
     * @param string $group
     */
    protected function setCurrentGroup($group)
    {
        $this->router->expects($this->any())->method('currentRouteAction')->willReturn("Controller@$group");
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
