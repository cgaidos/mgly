<?php

namespace Moowgly\Pilote\Controller;

use Moowgly\Lib\Controller\AbstractControllerPilote;
use Moowgly\Lib\Utils\ClientSilo;
use Moowgly\Lib\Utils\UUID;
use Moowgly\Pilote\Model\Host;
use Zaphpa\Request;
use Zaphpa\Response;
use Moowgly\Pilote\Model\UserConnected;

class HostController extends AbstractControllerPilote
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function dashboard(Request $req, Response $res)
    {

        $user = UserConnected::getInstance()->getUser();

        if(is_null($user['id_user'])){
            // return message
        }else{
            $host = Host::getHost($user['id_user']);
        }

        $twig = getTwig();
        echo $twig->render('/dashboard/dashboard.twig');
    }

    public function messages(Request $req, Response $res)
    {
        $twig = getTwig();
        echo $twig->render('/dashboard/messages/messages.twig');
    }

    public function conversation(Request $req, Response $res)
    {
        $twig = getTwig();
        echo $twig->render('/dashboard/messages/conversation.twig');
    }

    public function bookings(Request $req, Response $res)
    {
        $twig = getTwig();
        echo $twig->render('/dashboard/bookings/bookings.twig');
    }

    public function reviews(Request $req, Response $res)
    {
        $twig = getTwig();
        echo $twig->render('/dashboard/reviews/reviews.twig');
    }

    public function profile(Request $req, Response $res)
    {
        $user = UserConnected::getInstance()->getUser();

        if(is_null($user['id_user'])){
            // return message
        }else{
            $host = Host::getHost($user['id_user']);
        }

        $twig = getTwig();
        echo $twig->render('/dashboard/profile/profile.twig');
    }

    public function hostCard(Request $req, Response $res)
    {
        $twig = getTwig();
        echo $twig->render('/host-card/host-card.twig');
    }

    public function update(Request $req, Response $res)
    {
        // Bouchonner : créer un array qui va contenir les données du Guest pour tests

        $email = "gwendaldugue@gmail.com";
        $id = UUID::strToUuid($email);

        // if a value of the form is empty, set the value to #dtdm

        // We are waiting for an array Like this one

        $arrayData = array(
            "email" => $email,
            "family_name" => "Dugueté",
            "first_name" => "Gwendal"
        );

        $keys = array( "keys" => ['id_host' => $id]); // keys pour dynamodb

        $arrayData =  $keys + $arrayData;

        // crée un array 'elasticData';
        $elHost = new Host();
        $elHost = $elHost->parseDataForEl($arrayData);

        $arrayData = $arrayData + $elHost;

        $url = $_SERVER['PROJECT_SILO_URL'] . '/host/v1/' . $id;

        $client = ClientSilo::getInstance();
        $response = $client->put($url, ['form_params' => $arrayData]);

        $response = $response->getBody()->getContents();
    }

    public function sendMail(Request $req, Response $res)
    {

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
