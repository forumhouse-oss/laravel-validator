<?php namespace FHTeam\LaravelValidator\Tests\src\Validator\Eloquent;

use FHTeam\LaravelValidator\Tests\DatabaseTestBase;
use FHTeam\LaravelValidator\Tests\Fixture\Database\Models\Bear;

class EloquentModelValidatorTest extends DatabaseTestBase
{
    /**
     * @var Bear
     */
    protected $model;

    public function setUp()
    {
        parent::setUp();
        $this->model = new Bear();
        Bear::$validateBeforeSaving = true;
    }

    public function testIsValid()
    {
        $this->model->name = 'Valid bear';
        $this->model->type = 'Polar';
        $this->model->danger_level = 1;
        $this->assertTrue($this->model->save());
    }
}
