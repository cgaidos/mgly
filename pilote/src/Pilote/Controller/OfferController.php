<?php

namespace Moowgly\Pilote\Controller;

use Moowgly\Lib\Controller\AbstractControllerPilote;
use Moowgly\Lib\Utils\ClientSilo;
use Moowgly\Lib\Utils\UUID;
use Moowgly\Pilote\Model\Host;
use Zaphpa\Request;
use Zaphpa\Response;
use Moowgly\Pilote\Model\UserConnected;

class OfferController extends AbstractControllerPilote
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function createOffer(Request $req, Response $res)
    {

        $user = UserConnected::getInstance()->getUser();

        if(is_null($user['id_user'])){
            // return message
        }else{
            $host = Host::getHost($user['id_user']);
        }
        
        $twig = getTwig();
        echo $twig->render('/dashboard/offers/create-offer.twig');
    }

    public function offers(Request $req, Response $res)
    {

        $user = UserConnected::getInstance()->getUser();

        if(is_null($user['id_user'])){
            // return message
        }else{
            $host = Host::getHost($user['id_user']);
        }
        
        $twig = getTwig();
        echo $twig->render('/dashboard/offers/offers.twig');
    }

    public function insert(Request $req, Response $res)
    {

    }

    public function update(Request $req, Response $res)
    {

    }

    public function delete(Request $req, Response $res)
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
