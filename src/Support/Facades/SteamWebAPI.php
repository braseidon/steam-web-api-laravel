<?php namespace Braseidon\SteamWebAPI\Support\Facades;

use Illuminate\Support\Facades\Facade;

class SteamWebAPI extends Facade
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
