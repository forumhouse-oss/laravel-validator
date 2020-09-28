<?php

namespace FHTeam\LaravelValidator\Tests\Validator\Input;

use FHTeam\LaravelValidator\Tests\TestBase;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class ValidatesWhenResolvedTestBase
 *
 * @package FHTeam\LaravelValidator\Test\Input\ValidatesWhenResolved
 */
class InputValidatorTestBase extends TestBase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Request
     */
    protected $request;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Router
     */
    protected $router;

    /**
     * @var Factory
     */
    protected $validatorFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Redirector
     */
    protected $redirector;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|RedirectResponse
     */
    protected $redirectResponse;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->request = $this->getMockBuilder(Request::class)->getMock();

        $this->router = $this->getMockBuilder(Router::class)->disableOriginalConstructor()->getMock();

        $this->redirectResponse = $this->getMockBuilder(
            RedirectResponse::class
        )->disableOriginalConstructor()->getMock();

        $this->redirectResponse->expects($this->any())->method('withInput')->willReturnSelf();

        $this->redirector = $this->getMockBuilder(Redirector::class)->disableOriginalConstructor()->getMock();
        $this->redirector->expects($this->any())->method('route')->willReturn($this->redirectResponse);
        $this->redirector->expects($this->any())->method('action')->willReturn($this->redirectResponse);
        $this->redirector->expects($this->any())->method('withInput')->willReturnSelf();

        $this->validatorFactory = $this->app->make(Factory::class);
    }

    /**
     * @param array $data
     */
    public function setRequestData(array $data)
    {
        $this->request->expects($this->any())->method('all')->willReturn($data);
    }

    /**
     * @param array $data
     */
    public function setHeaderData(array $data)
    {
        $this->request->expects($this->any())->method('header')->willReturn($data);
    }

    /**
     * @param string $group
     */
    protected function setCurrentGroup($group)
    {
        $this->router->expects($this->any())->method('currentRouteAction')->willReturn(
            "Controller@$group"
        );
    }
}
