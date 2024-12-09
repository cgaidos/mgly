<?php

namespace Moowgly\Silo\Controller;

use Moowgly\Lib\Utils\Pdo;

class ActivityRoute
{
    private $db;
    private $table = 'activity';

    public function __construct()
    {
        $this->db = new Pdo('mysql:host=' . $_SERVER['PROJECT_DB_HOST'] . ';dbname=' . $_SERVER['PROJECT_DB_NAME'],
        $_SERVER['PROJECT_DB_USER'], $_SERVER['PROJECT_DB_PWD']);
    }

    public function get($req, $res)
    {
        header('Content-Type: application/json');
        echo $this->db->get('', $this->table);
    }

    public function getActCat($req, $res)
    {
        header('Content-Type: application/json');
        echo $this->db->getActCat('', $this->table);
    }
    
}
