<?php

namespace Moowgly\Pilote\Controller;

use Moowgly\Lib\Controller\AbstractControllerPilote;
use Moowgly\Lib\Utils\ClientSilo;
use Zaphpa\Request;
use Zaphpa\Response;

class SearchController extends AbstractControllerPilote
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function search(Request $req, Response $res)
    {
        echo getTwig()->render('/search-results/search-results-stay.twig');
    }

    public function searchOfferTest(Request $req, Response $res)
    {
        
        $url = $_SERVER['PROJECT_SILO_URL'] . '/searchOffer/v1/';

        $arrayData = array(
            "activity_code" => ["pai"],
            "geo_location" => ["lat" => 45.42524119999999 , "lon" => 11.912131999999929, "distance" => null  ],
            "date" => [ "from" => "2017-04-24", "to" => null],
            "sort" => [ "field" => "geo_location", "arrange" => "asc"]
        );


        $client = ClientSilo::getInstance();
        $response = $client->get($url, ['form_params' => $arrayData]);


        print_r($response->getBody()->getContents());
    }

    public function searchHostTest(Request $req, Response $res)
    {
        
        $url = $_SERVER['PROJECT_SILO_URL'] . '/searchHost/v1/';

        $arrayData = array(
            "activity_code" => ["pai"],
            "geo_location" => ["lat" => 45.42524119999999 , "lon" => 11.912131999999929, "distance" => null  ],
            "date" => [ "from" => "2017-04-24", "to" => null],
            "sort" => [ "field" => "geo_location", "arrange" => "asc"]
        );


        $client = ClientSilo::getInstance();
        $response = $client->get($url, ['form_params' => $arrayData]);


        print_r($response->getBody()->getContents());
    }

    /**
     * Acces forbidden page display.
     */
    public static function interdit($req, $res, $itemXML)
    {
        $twig = getTwig();
        echo $twig->render('interdit.html');
    }
}
