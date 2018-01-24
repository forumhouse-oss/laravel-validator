<?php namespace FHTeam\LaravelValidator\Tests\Validator\Input\ValidatesWhenResolved;

use FHTeam\LaravelValidator\Tests\Fixture\Input\FrontendControllerValidatorWhenResolvedFixture;
use FHTeam\LaravelValidator\Tests\Validator\Input\InputValidatorTestBase;
use FHTeam\LaravelValidator\Validator\Input\WhenResolved\FrontendControllerValidatorWhenResolved;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class FrontendControllerValidatorWhenResolvedTest
 *
 * @package FHTeam\LaravelValidator\Test\Input\ValidatesWhenResolved
 */
class FrontendControllerValidatorWhenResolvedTest extends InputValidatorTestBase
{
    public function setUp()
    {
        parent::setUp();
        $this->setCurrentGroup('group');
    }

    public function testValidateOk()
    {
        $this->setRequestData(['int' => 5]);
        $instance = $this->createControllerValidator();
        $this->assertInstanceOf(FrontendControllerValidatorWhenResolved::class, $instance);
    }

    public function testValidateRedirect()
    {
        $this->setRequestData(['int' => -1]);
        $this->setExpectedException(HttpResponseException::class);
        $this->createControllerValidator();
    }

    /**
     * @return mixed
     */
    protected function createControllerValidator()
    {
        return $this->app->make(
            FrontendControllerValidatorWhenResolvedFixture::class,
            [
                $this->validatorFactory,
                $this->request,
                $this->router,
                $this->redirector
            ]
        );
    }
}
