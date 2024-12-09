<?php

namespace Moowgly\Pilote\Controller;

use Moowgly\Lib\Controller\AbstractControllerPilote;
use Moowgly\Lib\Utils\ClientSilo;
use Moowgly\Lib\Utils\UUID;
use Zaphpa\Request;
use Zaphpa\Response;
use Moowgly\Pilote\Model\UserConnected;

class ReviewController extends AbstractControllerPilote
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function guestReview(Request $req, Response $res)
    {

    }

    public function hostReview(Request $req, Response $res)
    {

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
