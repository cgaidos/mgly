<?php

namespace Moowgly\Pilote\Controller;

use Moowgly\Lib\Controller\AbstractControllerPilote;
use Zaphpa\Request;
use Zaphpa\Response;

class FrontController extends AbstractControllerPilote
{
    public function show(Request $req, Response $res)
    {
        $page = str_replace('_', '/', $req->params['page']);

        echo getTwig()->render($page.'.twig', $req->data);
    }
}
