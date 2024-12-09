<?php
namespace Moowgly\Pilote\Model;

use Moowgly\Lib\Utils\ClientSilo;
use GuzzleHttp\Client;
use Moowgly\Lib\Utils\UUID;
use Moowgly\Lib\Utils\DomUtil;

abstract class AbstractModel
{

   public static function getType()
   {
       if (!defined('static::TYPE')) {
           throw new Exception('Constant TYPE is not defined on subclass '.get_class($this));
       } else {
           return static::TYPE;
       }
   }
   
   public static function getFeed($parameters, $id = '') {

       $client = ClientSilo::getInstance();
       
       $url = $_SERVER['PROJECT_SILO_URL'] . "/".self::getType()."/v1/".$id;
       
       $response = $client->get($url, $parameters);

       $feed = json_decode($response->getBody());
       
       return $feed;
   }


//    public static function insertFeed($parameters, $id = '') {
       
//        $client = ClientSilo::getInstance();
       
//        $url = $_SERVER['PROJECT_SILO_URL'] . "/".self::getCategory()."/v1/".$id;
// //         error_log(print_r($parameters, true));
       
//        $response = $client->post($url, $parameters);

//        $feed = json_decode($response->getBody());
       
//        return $feed;
//    }
   
}