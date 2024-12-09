<?php

namespace Moowgly\Silo\Middleware;

class AuthMiddleware extends \Zaphpa\BaseMiddleware
{
    public function preroute(&$req, &$res)
    {
        $authOK = false;

        // Test authentification

        // Dev mock
        $authOK = true;

        if (!$authOK) {
            $res->add('Not authenticated');
            $res->send('401');
        }
    }
}
