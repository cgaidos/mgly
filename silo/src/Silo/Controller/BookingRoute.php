<?php

namespace Moowgly\Silo\Controller;

use Moowgly\Lib\Controller\AbstractControllerSilo;

class BookingRoute extends AbstractControllerSilo
{
    
    public function __construct()
    {
        parent::__construct('Booking', 'Moowgly\Silo\Model\Booking');
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
        parent::delete($req, $res);
    }

}
