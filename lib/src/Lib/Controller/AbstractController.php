<?php

namespace Moowgly\Lib\Controller;

use Moowgly\Lib\Utils\Logger;

// Fix missing apache_request_headers function
if (!function_exists('apache_request_headers')) {
    // Fetch all HTTP request headers
    function apache_request_headers()
    {
        $headers = array();
        foreach (array_keys($_SERVER) as $skey) {
            if (substr($skey, 0, 5) == 'HTTP_') {
                $headername = str_replace(' ', '-', str_replace('_', ' ', substr($skey, 5)));
                $headers[$headername] = $_SERVER[$skey];
            }
        }

        return $headers;
    }
}

abstract class AbstractController
{
    protected $logger = null;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }
}
