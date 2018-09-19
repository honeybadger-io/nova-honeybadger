<?php

namespace HoneybadgerIo\NovaHoneybadger\Tests;

use HoneybadgerIo\NovaHoneybadger\Api;
use HoneybadgerIo\NovaHoneybadger\Honeybadger;
use Illuminate\Foundation\Auth\User;
use Mockery as m;

class ToolControllerTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = User::forceCreate([
            'name' => 'Marcel',
            'email' => 'marcel@beyondco.de',
            'password' => 'test'
        ]);

        $this->app['config']->set('services.honeybadger.project_id', 'PROJECT_ID');
    }

    private function getMock($projectId, $searchString, $limit = 10, $order = 'recent')
    {
        $mock = m::mock(Api::class);
        $mock->shouldReceive('getFaults')
            ->once()
            ->with($projectId, $searchString, $limit, $order)
            ->andReturn([]);

        return $mock;
    }

    /** @test */
    public function it_uses_user_id_for_default_context()
    {
        $mock = $this->getMock('PROJECT_ID', 'context.user_id:"' . $this->user->id . '"');

        $this->app->bind(Api::class, function () use ($mock) {
            return $mock;
        });

        $tool = new Honeybadger();

        $this
            ->json('GET', 'nova-vendor/honeybadgerio/honeybadger-laravel-nova/users/' . $this->user->id, $tool->element->meta)
            ->assertSuccessful();
    }

    /** @test */
    public function it_uses_model_values_for_context_values()
    {
        $mock = $this->getMock('PROJECT_ID', 'context.email:"' . $this->user->email . '"');

        $this->app->bind(Api::class, function () use ($mock) {
            return $mock;
        });

        $tool = Honeybadger::fromContextKeyAndAttribute('context.email', 'email');

        $this
            ->json('GET', 'nova-vendor/honeybadgerio/honeybadger-laravel-nova/users/' . $this->user->id, $tool->element->meta)
            ->assertSuccessful();
    }

    /** @test */
    public function it_uses_static_context_values()
    {
        $mock = $this->getMock('PROJECT_ID', 'context.email:"some_email"');

        $this->app->bind(Api::class, function () use ($mock) {
            return $mock;
        });

        $tool = Honeybadger::fromContextKeyAndValue('context.email', 'some_email');

        $this
            ->json('GET', 'nova-vendor/honeybadgerio/honeybadger-laravel-nova/users/' . $this->user->id, $tool->element->meta)
            ->assertSuccessful();
    }

    /** @test */
    public function it_uses_custom_search_parameters()
    {
        $mock = $this->getMock('PROJECT_ID', 'context.foo:"bar"');

        $this->app->bind(Api::class, function () use ($mock) {
            return $mock;
        });

        $tool = Honeybadger::fromSearchString('context.foo:"bar"');

        $this
            ->json('GET', 'nova-vendor/honeybadgerio/honeybadger-laravel-nova/users/' . $this->user->id, $tool->element->meta)
            ->assertSuccessful();
    }

    /** @test */
    public function it_uses_custom_search_parameters_in_combination_with_context_attributes()
    {
        $mock = $this->getMock('PROJECT_ID', 'context.user_id:"' . $this->user->id . '" -environment:"production"');

        $this->app->bind(Api::class, function () use ($mock) {
            return $mock;
        });

        $tool = (new Honeybadger)->withSearchString('-environment:"production"');

        $this
            ->json('GET', 'nova-vendor/honeybadgerio/honeybadger-laravel-nova/users/' . $this->user->id, $tool->element->meta)
            ->assertSuccessful();
    }

    /** @test */
    public function it_returns_404_for_invalid_models()
    {
        $tool = new Honeybadger();

        $this
            ->json('GET', 'nova-vendor/honeybadgerio/honeybadger-laravel-nova/users/100', $tool->element->meta)
            ->assertNotFound();
    }
}