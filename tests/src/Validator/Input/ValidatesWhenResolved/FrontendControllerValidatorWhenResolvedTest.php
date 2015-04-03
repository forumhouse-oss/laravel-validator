<?php namespace FHTeam\LaravelValidator\Test\Validator\Input\ValidatesWhenResolved;

use FHTeam\LaravelValidator\Test\Fixture\Input\FrontendControllerValidatorWhenResolvedFixture;
use FHTeam\LaravelValidator\Test\Validator\Input\InputValidatorTestBase;
use FHTeam\LaravelValidator\Validator\Input\WhenResolved\FrontendControllerValidatorWhenResolved;
use Illuminate\Http\Exception\HttpResponseException;

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
