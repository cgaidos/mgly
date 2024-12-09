<?php
namespace Moowgly\Lib\Utils;

use GuzzleHttp\Client;

class ClientSilo
{

    private static $_instance = null;

    private static $_client;

    private function __construct()
    {}
    
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new ClientSilo();
            self::$_client = new Client();
        }
        return self::$_instance;
    }

    public function getEmitter()
    {
        return self::$_client->getEmitter();
    }
    
    public function get($url, $options = array())
    {
        return self::$_client->get($url, array_merge($options, ['http_errors' => false]));
    }
    
    public function post($url, $options)
    {
        return self::$_client->post($url, $options);
    }

    public function put($url, $options)
    {
        return self::$_client->put($url, $options);
    }

    public function delete($url, $options = array())
    {
        return self::$_client->delete($url);
    }
}