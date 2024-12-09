<?php

namespace Moowgly\Silo\Controller;

use Moowgly\Lib\Controller\AbstractControllerSilo;

class GuestRoute extends AbstractControllerSilo
{
    
    public function __construct()
    {
        parent::__construct('Guest', 'Moowgly\Silo\Model\Guest');
    }

    public function get($req, $res)
    {
        parent::get($req, $res);
    }

    public function insert($req, $res)
    {
        parent::post($req, $res);
    }

    public function update($req, $res)
    {
        parent::put($req, $res);
    }

    public function delete($req, $res)
    {
        error_log(print_r($req->data, true));
        parent::delete($req, $res);
    }

}
