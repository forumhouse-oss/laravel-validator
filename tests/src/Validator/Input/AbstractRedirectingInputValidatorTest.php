<?php

namespace FHTeam\LaravelValidator\Tests\Validator\Input;

use Exception;
use FHTeam\LaravelValidator\Tests\Fixture\AbstractRedirectingInputValidatorFixture;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\RedirectResponse;

/**
 * Class AbstractInputValidatorTest
 *
 * @package FHTeam\LaravelValidator\Test\Input
 */
class AbstractRedirectingInputValidatorTest extends InputValidatorTestBase
{
    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testNoGroup()
    {
        $this->setCurrentGroup('route');
        $validator = $this->createValidator();
        $this->setExpectedException(Exception::class);
        $redirect = $validator->getRedirect();
    }

    public function testGetErrorRedirect()
    {
        $this->setCurrentGroup('simple_route');
        $validator = $this->createValidator();
        $redirect = $validator->getRedirect();
        $this->assertInstanceOf(RedirectResponse::class, $redirect);
        //TODO: this needs further testing
    }

    /**
     * @return AbstractRedirectingInputValidatorFixture
     */
    protected function createValidator()
    {
        return $this->app->make(
            AbstractRedirectingInputValidatorFixture::class,
            [
                Container::getInstance()->make(Factory::class),
                $this->request,
                $this->router,
                $this->redirector,
            ]
        );
    }
}
