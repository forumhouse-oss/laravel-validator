<?php namespace FHTeam\LaravelValidator\Tests\Validator\Input\RoutingMiddleware;

use FHTeam\LaravelValidator\Tests\Fixture\Input\FrontendControllerValidatorMiddlewareFixture;
use FHTeam\LaravelValidator\Tests\Validator\Input\InputValidatorTestBase;
use FHTeam\LaravelValidator\Validator\Input\RoutingMiddleware\FrontendControllerValidatorMiddleware;
use Illuminate\Contracts\Routing\Middleware;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class FrontendControllerValidatorMiddlewareTest
 *
 * @package FHTeam\LaravelValidator\Test\Input\RoutingMiddleware
 */
class FrontendControllerValidatorMiddlewareTest extends InputValidatorTestBase
{
    public function setUp()
    {
        parent::setUp();
        $this->setCurrentGroup('group');
    }

    public function testInstanceOf()
    {
        $this->setRequestData(['int' => 5]);
        $instance = $this->createControllerValidator();
        $this->assertInstanceOf(Middleware::class, $instance);
        $this->assertInstanceOf(FrontendControllerValidatorMiddleware::class, $instance);
    }

    public function testValidateOk()
    {
        $this->setRequestData(['int' => 5]);
        $instance = $this->createControllerValidator();
        $this->assertNull(
            $instance->handle(
                $this->request,
                function ($value) {
                    return $value;
                }
            )
        );
    }

    public function testValidateRedirect()
    {
        $this->setRequestData(['int' => -1]);
        $instance = $this->createControllerValidator();

        /** @var RedirectResponse $result */
        $result = $instance->handle(
            $this->request,
            function ($value) {
                return $value;
            }
        );

        $this->assertInstanceOf(RedirectResponse::class, $result);
    }

    /**
     * @return Middleware
     */
    protected function createControllerValidator()
    {
        return $this->app->make(
            FrontendControllerValidatorMiddlewareFixture::class,
            [
                $this->validatorFactory,
                $this->request,
                $this->router,
                $this->redirector
            ]
        );
    }
}
