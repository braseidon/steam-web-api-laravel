<?php namespace Braseidon\SteamAPI\Support\Facades;

use Illuminate\Support\Facades\Facade;

class SteamAPI extends Facade
{

    /**
    * Get the registered name of the component.
    *
    * @return string
    */
    protected static function getFacadeAccessor()
    {
        return 'braseidon.steam-web-api.php';
    }
}
