<?php namespace App\Poseidon\SteamAPI\Traits;

trait Cached
{

    /**
     * @var array
     */
    public static $cache = [];

    /**
     * Returns whether the item schema for the given application ID and
     * language is already cached
     *
     * @param int $appId The application ID of the game
     * @param string $language The language of the item schema
     * @return bool <var>true</var> if the object with the given ID is already
     *         cached
     */
    public static function isCached($appId, $language)
    {
        return array_key_exists($appId, self::$cache) &&
               array_key_exists($language, self::$cache[$appId]);
    }

    /**
     * Clears the item schema cache
     */
    public static function clearCache()
    {
        self::$cache = [];
    }

    /**
     * Saves this item schema in the cache
     *
     * @return bool <var>false</var> if this item schema is already cached
     */
    private function cache()
    {
        if (array_key_exists($this->appId, self::$cache) &&
            array_key_exists($this->language, self::$cache[$this->appId])) {
            return false;
        }

        self::$cache[$this->appId][$this->language] = $this;

        return true;
    }
}
