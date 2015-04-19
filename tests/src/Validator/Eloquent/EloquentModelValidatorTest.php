<?php namespace FHTeam\LaravelValidator\Tests\src\Validator\Eloquent;

use FHTeam\LaravelValidator\Tests\DatabaseTestBase;
use FHTeam\LaravelValidator\Tests\Fixture\Database\Models\Bear;
use FHTeam\LaravelValidator\Validator\ValidationException;

/**
 * Class EloquentModelValidatorTest
 *
 * @group   medium
 * @package FHTeam\LaravelValidator\Tests\src\Validator\Eloquent
 */
class EloquentModelValidatorTest extends DatabaseTestBase
{
    /**
     * @var Bear
     */
    protected $model;

    public function setUp()
    {
        parent::setUp();
        Bear::$validateBeforeSaving = true;
        Bear::bootEloquentValidatingTrait();
        $this->model = new Bear();
    }

    public function testIsValid()
    {
        $this->model->name = 'Valid bear';
        $this->model->type = 'Polar';
        $this->model->danger_level = 1;
        $this->assertTrue($this->model->save());
    }

    public function testIsXInvalid()
    {
        $this->model->name = 'Valid bear';
        $this->model->type = 'XXX PornoStar Bear';
        $this->model->danger_level = "XXX";
        $this->setExpectedException(ValidationException::class);
        $this->model->save();
    }
}
