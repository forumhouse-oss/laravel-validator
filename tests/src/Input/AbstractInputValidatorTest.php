<?php

namespace FHTeam\LaravelValidator\Test\Input;

use FHTeam\LaravelValidator\Input\AbstractInputValidator;
use FHTeam\LaravelValidator\Test\TestBase;
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
    /**
     * @var AbstractInputValidator
     */
    protected $validator;

    protected $dataInput = ['inputKey1' => 'inputValue1', 'inputKey2' => 'inputValue2'];

    protected $dataHeader = ['headerKey1' => 'headerValue1', 'headerKey2' => 'headerValue2'];


    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        /** @var PHPUnit_Framework_MockObject_MockObject|Request $request */
        $request = $this->getMockBuilder(Request::class)->getMock();

        $request->expects($this->any())->method('all')->willReturn(
            $this->dataInput
        );

        $request->expects($this->any())->method('header')->willReturn(
            $this->dataHeader
        );

        /** @var PHPUnit_Framework_MockObject_MockObject|Router $router */
        $router = $this->getMockBuilder(Router::class)->disableOriginalConstructor()->getMock();

        $router->expects($this->any())->method('currentRouteAction')->willReturn('Controller@group');

        $this->validator = new AbstractInputValidator(
            Container::getInstance()->make(Factory::class),
            $request,
            $router
        );
    }

    public function testCollectDataInput()
    {
        $this->validator->setInputTypes(AbstractInputValidator::VALIDATE_INPUT);
        $this->assertEquals($this->dataInput, $this->validator->collectData());
    }

    public function testCollectDataInputAndHeader()
    {
        $this->validator->setInputTypes(
            AbstractInputValidator::VALIDATE_INPUT | AbstractInputValidator::VALIDATE_HEADERS
        );
        $this->assertEquals($this->dataInput + $this->dataHeader, $this->validator->collectData());
    }
}
