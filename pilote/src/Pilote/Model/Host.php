<?php
namespace Moowgly\Pilote\Model;

use Moowgly\Lib\Utils\ClientSilo;
use GuzzleHttp\Client;
use Moowgly\Lib\Utils\UUID;
use Moowgly\Lib\Utils\DomUtil;

class Host extends AbstractModel
{
   const TYPE = 'host';

  /**
  * Matching between dynamodb and elasticsearch fields.
  */
  private $arrLinkHost = array(

    "keys" => "id", // Si nous récupérons la key : 'keys' c'est qu'il faut transformer le tableau pour update / get / delete
    "id_host" => "id",
    "email" => "email",
    "first_name" => "first_name",
    "family_name" => "family_name",
    "who" => "who",
    "address" => "address",
    "location" => "location",
    "home_type" => "home_type",
    "home_features" => "home_features",
    "meal_specialties" => "meal_specialties",
    // "parents" => "",
    // "kids" => "",
    // "family_description" => "",
    "activity" => "activity", 
    "media" => "media",
    "wishes" => "wishes",
    "availability" => "availability",
    "price_per_day" => "price_per_day",
    "review" => "review"
    
  );

  public static function getHost($id_host)
  {

        $data = array("id_host" =>  $id_host);

        $parameters = ['form_params' => $data];

        return parent::getFeed($parameters, $id_host);
  }

  public function parseDataForEl($arrHost)
  {
    // Array for elasticsearch;
    $arrElHost = array();

    foreach($arrHost as $hostKey => $hostValue) { 
        if(array_key_exists($hostKey, $this->arrLinkHost)){
            
            if(is_array($hostValue) && array_key_exists('id_host', $hostValue)){
              
              $hostValue = $hostValue['id_host'];
            }

            $arrElHost["elasticData"][$this->arrLinkHost[$hostKey]] = $hostValue;
        }

    }

    return $arrElHost;
  }


   // public static function insertGuest($data)
   // {

   //      $id_guest = UUID::strToUuid($data['email']);

   //      $data = array("id_guest" =>  $id_guest, "email" => $email);

   //      $parameters = ['form_params' => $data];

   //      return parent::insertFeed($parameters, $id_guest);
   // }

}