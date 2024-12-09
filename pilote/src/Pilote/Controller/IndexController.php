<?php

namespace Moowgly\Pilote\Controller;

use Moowgly\Lib\Controller\AbstractControllerPilote;
use Moowgly\Lib\Utils\ClientSilo;
use Moowgly\Lib\Utils\UUID;
use Zaphpa\Request;
use Zaphpa\Response;

class IndexController extends AbstractControllerPilote
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $req, Response $res)
    {
        $twig = getTwig();
        echo $twig->render('home/home.twig');
//        echo $twig->render('moowgly.html');
//        echo $twig->render('smallgly.html');
    }

    public function mission(Request $req, Response $res)
    {
        $twig = getTwig();
        echo $twig->render('layouts/mission.twig');
    }

    public function insert(Request $req, Response $res)
    {
        $url = $_SERVER['PROJECT_SILO_URL'] . '/user/v1/';

        $client = ClientSilo::getInstance();
		$response = $client->post($url, ['form_params' => $req->data]);
    }

    public function update(Request $req, Response $res)
    {
        $url = $_SERVER['PROJECT_SILO_URL'] . '/user/v1/' . $req->data['id'];
        $client = ClientSilo::getInstance();
		$response = $client->put($url, ['form_params' => $req->data]);
    }

    public function delete(Request $req, Response $res)
    {
        $url = $_SERVER['PROJECT_SILO_URL'] . '/user/v1/' . $req->data['id'];
        $client = ClientSilo::getInstance();
		$response = $client->delete($url, ['form_params' => $req->data]);
    }

    public function createUUID($param)
    {
        $UUID = UUID::strToUuid($param);

        return $UUID;
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
