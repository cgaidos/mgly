<?php

namespace Moowgly\Pilote\Controller;

use Moowgly\Lib\Controller\AbstractControllerPilote;
use Moowgly\Lib\Utils\ClientSilo;
use Zaphpa\Request;
use Zaphpa\Response;
use Moowgly\Lib\Utils\UUID;
use Moowgly\Pilote\Model\Reference;
use Moowgly\Pilote\Model\Host;
use Moowgly\Pilote\Model\UserConnected;
use Moowgly\Pilote\Controller\HostController;

class RegisterController extends AbstractControllerPilote
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
        // Get activity category
        $req->params['activityCat'] = Reference::getReference('getActCat');

        // Get activity detail
        $req->params['activity'] = Reference::getReference('getActivity');

        // Get language
        $req->params['language'] = Reference::getReference('language');

        // Get currency
        $req->params['currency'] = Reference::getReference('currency');

        // Get country for google map api boundary
        $req->params['country'] = Reference::getReference('country');

        // Get certificate
        $req->params['certificate'] = Reference::getReference('certificate');

        echo getTwig()->render('/become-host/index.twig',$req->params);
    }

    /**
     * Data Manipulation.
     */

    public function insertHost(Request $req, Response $res)
    {
        // Bouchonner : créer un array qui va contenir les données du Host pour tests

        // $email = "christos@gmail.com";

        $user = UserConnected::getInstance()->getUser();

        // $id = UUID::strToUuid($user);

        $req->data['id_host'] = $user['id_user'];
        $req->data['email'] = $user['email'];
        $req->data['family_name'] = $user['family_name'];
        $req->data['first_name'] = $user['first_name'];

        // crée un array 'elasticData';
        $elHost = new Host();
        $elHost = $elHost->parseDataForEl($req->data);


        $arrayData = $req->data + $elHost;

        $url = $_SERVER['PROJECT_SILO_URL'] . '/host/v1/' . $user['id_user'];

        try {
            $client = ClientSilo::getInstance();
            $response = $client->post($url, ['form_params' => $arrayData]);
        } catch (\Exception $e) {
            return http_response_code(500);
        }

        if ($response->getStatusCode() == '200') {
            $done = array('validation' => 'success', 'type' => 'alert-success');
            http_response_code(201);
        } else {
            $done = array('validation' => 'error', 'type' => 'alert-error');
            http_response_code(422);
        }

        echo json_encode($done);
    }

    /**
     * Acces forbidden page display.
     */
    public static function interdit($req, $res, $itemXML)
    {
        echo getTwig()->render('interdit.html');
    }
}
