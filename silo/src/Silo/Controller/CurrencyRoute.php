<?php

namespace Moowgly\Silo\Controller;

use Moowgly\Lib\Utils\Pdo;

class CurrencyRoute
{
    private $db;
    private $table = 'currency';

    public function __construct()
    {
        $this->db = new Pdo('mysql:host=' . $_SERVER['PROJECT_DB_HOST'] . ';dbname=' . $_SERVER['PROJECT_DB_NAME'],
        $_SERVER['PROJECT_DB_USER'], $_SERVER['PROJECT_DB_PWD']);
    }

    public function get($req, $res)
    {
        echo $this->db->get('', $this->table);
    }
}
