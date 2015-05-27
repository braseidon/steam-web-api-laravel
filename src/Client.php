<?php namespace Braseidon\SteamWebAPI;

use Braseidon\SteamWebAPI\Traits\Cached;
use Exception;

class WebClient
{

    use Cached;

    /**
     * @var string
     */
    private static $apiKey = null;

    /**
     * @var WebClient
     */
    protected static $instance = null;

    /**
     * @var bool
     */
    protected static $secure = true;

    /**
     * Private constructor to prevent direct usage of <var>WebClient</var>
     * instances
     */
    private function __construct()
    {
    }

    /**
     * Returns the Steam Web API key
     *
     * @return string The Steam Web API key
     */
    public function getApiKey()
    {
        return self::$apiKey;
    }

    /**
     * Sets the Steam Web API key
     *
     * @param string $apiKey The 128bit API key that has to be requested from http://steamcommunity.com/dev     *
     * @throws Exception if the given API key is not a valid 128bit hexadecimal string
     */
    public static function setApiKey($apiKey)
    {
        if ($apiKey !== null && ! preg_match('/^[0-9A-F]{32}$/', $apiKey)) {
            throw new InvalidArgumentException('The API Key in the config is required and must be valid!');
        }

        self::$apiKey = $apiKey;
    }

    /**
     * Get the JSON from the API
     *
     * @param  string  $interface
     * @param  string  $method
     * @param  integer $version
     * @param  array   $params
     * @return _JSON
     */
    public static function getJSON($interface, $method, $version = 1, array $params = [])
    {
        return self::instance()->_getJSON($interface, $method, $version, $params);
    }

    /**
     * Fetches JSON data from Steam Web API using the specified interface,
     * method and version. Additional parameters are supplied via HTTP GET.
     *
     * @param  string  $interface
     * @param  string  $method
    * @param  integer $version
     * @param  array  $params
     * @return stdClass
     */
    protected function _getJSON($interface, $method, $version = 1, $params = null)
    {
        return $this->load('json', $interface, $method, $version, $params);
    }

    /**
     * Fetches JSON data from Steam Web API using the specified interface, method and version.
     *
     * @param  string   $interface
     * @param  string   $method
     * @param  integer  $version
     * @param  array    $params
     * @return stdClass
     */
    public static function getJSONData($interface, $method, $version = 1, array $params = [])
    {
        return self::instance()->_getJSONData($interface, $method, $version, $params);
    }

    /**
     * Fetches JSON data from Steam Web API using the specified interface, method and version.
     *
     * @param  string   $interface
     * @param  string   $method
     * @param  integer  $version
     * @param  array    $params
     * @return stdClass
     */
    protected function _getJSONData($interface, $method, $version = 1, array $params = [])
    {
        $data   = self::getJSON($interface, $method, $version, $params);
        $result = object_get(json_decode($data), 'result');

        if ($result->status !== 1) {
            throw new Exception('Status was bad.');
        }

        return $result;
    }

    /**
     * Load the remote Steam API
     *
     * @param  string  $format
     * @param  string  $interface
     * @param  string  $method
     * @param  integer $version
     * @param  array   $params
     * @return string
     */
    public static function load($format, $interface, $method, $version = 1, $params = null)
    {
        return self::instance()->_load($format, $interface, $method, $version, $params);
    }

    /**
     * Load the remote Steam API
     *
     * @param  string  $format
     * @param  string  $interface
     * @param  string  $method
     * @param  integer $version
     * @param  array   $params
     * @return string
     */
    protected function _load($format, $interface, $method, $version = 1, array $params = [])
    {
        $version = str_pad($version, 4, '0', STR_PAD_LEFT);
        $protocol = (self::$secure) ? 'https' : 'http';
        $url = "$protocol://api.steampowered.com/$interface/$method/v$version/";

        $params['format'] = $format;
        if (self::$apiKey !== null) {
            $params['key'] = self::$apiKey;
        }

        if (count($params)) {
            $url = $url . '?' . http_build_query($params);
        }

        return self::request($url);
    }

    /**
     * Perform the API Request
     *
     * @param  string $url
     * @return string
     */
    protected function request($url)
    {
        $data = @file_get_contents($url);

        if (empty($data)) {
            preg_match('/^.* (\d{3}) (.*)$/', $http_response_header[0], $http_status);

            if ($http_status[1] == 401) {
                throw new Exception('Unauthorized.');
            }

            throw new Exception('Http error', $http_status[1], $http_status[2]);
        }

        return $data;
    }

    /**
     * Returns a singleton instance of an internal <var>WebClient</var> object
     *
     * @return WebClient The internal <var>WebClient</var> instance
     */
    private static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new WebClient();
        }

        return self::$instance;
    }

    /**
     * Returns a raw list of interfaces and their methods that are available in
     * Steam's Web API
     *
     * This can be used for reference when accessing interfaces and methods
     * that have not yet been implemented by Steam Condenser.
     *
     * @return array The list of interfaces and methods
     */
    public static function getInterfaces()
    {
        $data = self::getJSON('ISteamWebAPIUtil', 'GetSupportedAPIList');

        return object_get(json_decode($data), 'apilist.interfaces');
    }
}
