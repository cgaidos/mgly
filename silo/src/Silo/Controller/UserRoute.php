<?php

namespace Moowgly\Silo\Controller;

use Moowgly\Lib\Utils\Pdo;

class UserRoute
{
    private $db;
    private $table = 'user';

    public function __construct()
    {
        $this->db = new Pdo('mysql:host=' . $_SERVER['PROJECT_DB_HOST'] . ';dbname=' . $_SERVER['PROJECT_DB_NAME'],
        $_SERVER['PROJECT_DB_USER'], $_SERVER['PROJECT_DB_PWD']);
    }

    public function get($req, $res)
    {
        echo $this->db->get($req->params['id'], $this->table);
    }

    public function checkUserToken($req, $res)
    {
        echo $this->db->getUserToken($req->data, $this->table);
    }

    public function checkUserPwdEmail($req, $res)
    {
        echo $this->db->getUserPwdEmail($req->data, $this->table);
    }

    public function checkUserEmail($req, $res)
    {
        echo $this->db->getUserEmail($req->data, $this->table);
    }

    public function post($req, $res)
    {
        echo $this->db->post($req->data, $this->table);
    }

    public function put($req, $res)
    {
        echo $this->db->put($req->data, $this->table);
    }

    public function delete($req, $res)
    {
        // parent::delete($req, $res);
    }
}
