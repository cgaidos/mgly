<?php
namespace Moowgly\Pilote\Model;

use Moowgly\Lib\Utils\ClientSilo;
use GuzzleHttp\Client;
use Moowgly\Lib\Utils\UUID;

class Reference extends AbstractModel
{
    const TYPE = 'reference';

    public static function getReference($table)
    {
        // Get activity category
        $url = $_SERVER['PROJECT_SILO_URL'] . '/' . $table . '/v1/';

        $client = ClientSilo::getInstance();
        $result = $client->get($url)->getBody()->getContents();

        return json_decode($result);
    }
}