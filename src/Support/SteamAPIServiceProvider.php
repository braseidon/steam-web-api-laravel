<?php namespace Braseidon\SteamAPI\Support;

use Illuminate\Support\ServiceProvider;

class SteamAPIServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../../config/braseidon.steam-web-api.php';
        $this->mergeConfigFrom($configPath, 'braseidon.steam-web-api');
        $this->publishes([$configPath => config_path('braseidon.steam-web-api.php')], 'config');

        $this->app->bindShared('braseidon.steam-web-api', function ($app) {
            $apiKey = config('braseidon.steam-web-api.api_key');

            return new Client($app->make('Illuminate\Cache\CacheManager'), $apiKey);
        });

        $this->app->alias('braseidon.steam-web-api', 'Braseidon\SteamAPI\Client');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['braseidon.steam-web-api'];
    }
}
