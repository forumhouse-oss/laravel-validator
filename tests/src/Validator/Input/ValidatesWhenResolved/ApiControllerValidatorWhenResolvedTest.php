<?php namespace FHTeam\LaravelValidator\Tests\Validator\Input\ValidatesWhenResolved;

use FHTeam\LaravelValidator\Tests\Fixture\Input\ApiControllerValidatorWhenResolvedFixture;
use FHTeam\LaravelValidator\Tests\Validator\Input\InputValidatorTestBase;
use FHTeam\LaravelValidator\Validator\Input\WhenResolved\ApiControllerValidatorWhenResolved;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class ApiControllerValidatorWhenResolvedTest
 *
 * @package FHTeam\LaravelValidator\Test\Input\ValidatesWhenResolved
 */
class ApiControllerValidatorWhenResolvedTest extends InputValidatorTestBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setCurrentGroup('group');
    }

    public function testValidateOk()
    {
        $this->setRequestData(['int' => 5]);
        $instance = $this->createControllerValidator();
        $this->assertInstanceOf(ApiControllerValidatorWhenResolved::class, $instance);
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
            ApiControllerValidatorWhenResolvedFixture::class,
            [
                $this->validatorFactory,
                $this->request,
                $this->router,
                $this->app->make(ResponseFactory::class)
            ]
        );
    }
}
