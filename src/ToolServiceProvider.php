<?php

namespace HoneybadgerIo\NovaHoneybadger;

use GuzzleHttp\Client;
use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('honeybadger-laravel-nova', __DIR__ . '/../dist/js/tool.js');
            Nova::style('honeybadger-laravel-nova', __DIR__ . '/../dist/css/tool.css');
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/honeybadgerio/honeybadger-laravel-nova')
            ->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Api::class, function () {
            $client = new Client([
                'base_uri' => Api::API_URL,
                'verify' => false,
                'auth' => [config('services.honeybadger.token'), '']
            ]);

            return new Api($client);
        });
    }
}
