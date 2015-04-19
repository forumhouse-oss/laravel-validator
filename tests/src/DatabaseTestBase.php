<?php

namespace FHTeam\LaravelValidator\Tests;

use Exception;
use FHTeam\LaravelValidator\Tests\Fixture\Database\Seeds\DatabaseSeeder;
use Orchestra\Testbench\TestCase;

class DatabaseTestBase extends TestCase
{
    protected $migrationsPath = '../fixture/Database/Migrations';

    public function setUp()
    {
        parent::setUp();

        if (!$this->migrationsPath) {
            throw new Exception("Migrations path does not exist");
        }

        $this->artisan(
            'migrate',
            [
                '--database' => 'test',
                '--path' => $this->migrationsPath,
            ]
        );

        $this->artisan(
            'db:seed',
            [
                '--class' => DatabaseSeeder::class
            ]
        );
    }

    public function tearDown()
    {
        $this->artisan(
            'migrate:reset',
            [
                '--database' => 'test'
            ]
        );
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('database.default', 'test');
        $app['config']->set(
            'database.connections.test',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );
    }

    /**
     * Get base path.
     *
     * @return string
     * @throws Exception
     */
    protected function getBasePath()
    {
        $basedir = realpath(__DIR__.'/../../tests/laravel');
        if (!$basedir) {
            throw new Exception("Base directory does not exist");
        }

        return $basedir;
    }
}
