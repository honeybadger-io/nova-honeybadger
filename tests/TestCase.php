<?php

namespace HoneybadgerIo\NovaHoneybadger\Tests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use Orchestra\Testbench\TestCase as Orchestra;
use HoneybadgerIo\NovaHoneybadger\ToolServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();

        Route::middlewareGroup('nova', []);

        $this->setUpDatabase();

        Nova::$resources = [new TestResource(null)];
    }

    protected function getPackageProviders($app)
    {
        return [
            ToolServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('app.key', 'base64:6Cu/ozj4gPtIjmXjr8EdVnGFNsdRqZfHfVjQkmTlg4Y=');
    }

    protected function setUpDatabase()
    {
        $this->loadLaravelMigrations(['--database' => 'sqlite']);
    }
}

class TestResource extends Resource {

    public static $model = User::class;

    public static function uriKey()
    {
        return 'users';
    }

    public function fields($request)
    {
        return [];
    }
}