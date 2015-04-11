<?php

namespace FHTeam\LaravelValidator\Tests;

use Exception;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;

/**
 * Class LaravelAmqpTestBase
 *
 * @package Forumhouse\LaravelAmqp\Tests
 */
class TestBase extends TestCase
{
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

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        //
    }

    /**
     * Get package providers.  At a minimum this is the package being tested, but also
     * would include packages upon which our package depends, e.g. Cartalyst/Sentry
     * In a normal app environment these would be added to the 'providers' array in
     * the config/app.php file.
     *
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [];
    }

    /**
     * Getting rid of unused service providers
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getApplicationProviders($app)
    {
        return [
            'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
            'Illuminate\Routing\ControllerServiceProvider',
            'Illuminate\Cookie\CookieServiceProvider',
            'Illuminate\Database\DatabaseServiceProvider',
            'Illuminate\Filesystem\FilesystemServiceProvider',
            'Orchestra\Database\MigrationServiceProvider',
            'Illuminate\Pipeline\PipelineServiceProvider',
            'Illuminate\Session\SessionServiceProvider',
            'Illuminate\Translation\TranslationServiceProvider',
            'Illuminate\Validation\ValidationServiceProvider',
            'Illuminate\View\ViewServiceProvider',
        ];
    }

    protected function getApplicationAliases($app)
    {
        $result = parent::getApplicationAliases($app);

        return $result;
    }
}
