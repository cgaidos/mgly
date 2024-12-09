<?php

namespace Moowgly\Silo\Controller;

use Moowgly\Lib\Utils\Pdo;

class LanguageRoute
{
    private $db;
    private $table = 'language';

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
