<?php
namespace Moowgly\Pilote\Model;

use Moowgly\Lib\Utils\ClientSilo;
use GuzzleHttp\Client;
use Moowgly\Lib\Utils\UUID;
use Moowgly\Lib\Utils\DomUtil;

class Guest extends AbstractModel
{
   const TYPE = 'guest';

  /**
  * Matching data for dynamodb 
  */
  private $arrLinkGuest = array(

    "id" => "id_guest",
    "id_guest" => "id_guest",
    "email" => "email",
    "first_name" => "first_name",
    "family_name" => "family_name",
    "phone" => "who",
    "address" => "address",
    "location" => "location",
    
  );

  public static function getGuest($id_guest)
  {

        $data = array("id_guest" =>  $id_guest);

        $parameters = ['form_params' => $data];

        return parent::getFeed($parameters, $id_guest);
  }

  public function parseDataForDy($arrGuest)
  {
    // Array for Dyanmodb;
    $arrDyGuest = array();

    foreach($arrGuest as $guestKey => $guestValue) { 
        if(array_key_exists($guestKey, $this->arrLinkGuest)){

            $arrDyGuest[$this->arrLinkGuest[$guestKey]] = $guestValue;
        }

    }

    return $arrDyGuest;
  }


   // public static function insertGuest($data)
   // {

   //      $id_guest = UUID::strToUuid($data['email']);

   //      $data = array("id_guest" =>  $id_guest, "email" => $email);

   //      $parameters = ['form_params' => $data];

   //      return parent::insertFeed($parameters, $id_guest);
   // }

}