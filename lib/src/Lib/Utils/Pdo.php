<?php

namespace Moowgly\Lib\Utils;

class Pdo
{
    private $db;

    private static $_instance = null;

    /**
     * Constructor.
     */
    public function __construct($dsn, $user, $passwd)
    {
        $options = array(
          \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        );
        $connection = new \PDO($dsn, $user, $passwd, $options);
        $this->db = $connection;
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        self::$_instance = $this;
    }

    /**
     *
     * @return Database instance.
     */
    public static function getInstance()
    {
        return self::$_instance;
    }

    public function get($id, $table)
    {
        if ($id !== '') {
            $stmt = $this->db->prepare("SELECT * FROM $table WHERE id = :id LIMIT 1");
            $stmt->execute(compact('id'));
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM $table");
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return json_encode($result);
    }

    public function getUserToken($data, $table)
    {
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE id = :id AND token = :token ");
        $stmt->execute(array(
            'id' => $data['id'],
            'token' => $data['token']
        ));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    public function getUserPwdEmail($data, $table)
    {
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE email = :email AND password = :password AND active = '1' ");
        $stmt->execute(array(
            'email' => $data['email'],
            'password' => $data['password']
        ));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    public function getUserEmail($data, $table)
    {
        $stmt = $this->db->prepare("SELECT * FROM $table WHERE email = :email");
        $stmt->execute(array(
            'email' => $data['email']
        ));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    public function getActCat($data, $table)
    {
        $stmt = $this->db->prepare("SELECT DISTINCT act_cat_code, act_cat_en  FROM $table ORDER BY act_cat_en ASC ");
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    public function post($data, $table)
    {
        // Remove raw http data
        if(array_key_exists('_RAW_HTTP_DATA', $data)){
            unset($data['_RAW_HTTP_DATA']);
        }
        
        $stmtSQL = "INSERT INTO $table (";
        $stmtValues = " VALUES (";
        foreach($data as $col => $value) {
            $stmtSQL .= $col . ", ";
            $stmtValues .= ":" . $col . ", ";
        }
        $stmtSQL = rtrim($stmtSQL, ", ") . ")";
        $stmtValues = rtrim($stmtValues, ", ") . ")";
        $stmt = $this->db->prepare($stmtSQL . $stmtValues);

        foreach($data as $col => $value) {
            $stmt->bindValue(":" . $col, $value);
        }

        return $stmt->execute();
    }

    public function put($data, $table)
    {
        // Remove raw http data
        if(array_key_exists('_RAW_HTTP_DATA', $data)){
            unset($data['_RAW_HTTP_DATA']);
        }
        
        $stmtSQL = "UPDATE $table SET ";
        $stmtWhere = " WHERE ";
        foreach($data as $col => $value) {
            if ($col == 'id') {
                $stmtWhere .= $col . "=:" . $col;
            } else {
                $stmtSQL .= $col . "=:" . $col . ", ";
            }
        }
        $stmtSQL = rtrim($stmtSQL, ", ") . $stmtWhere;
        $stmt = $this->db->prepare($stmtSQL);

        foreach($data as $col => $value) {
            $stmt->bindValue(":" . $col, $value);
        }

        return $stmt->execute();
    }

    public function getField($id, $table, $field = 'md5')
    {
        $fieldValue = null;
        if ($id) {
            $stmt = $this->db->prepare("SELECT $field FROM $table WHERE id=:id");
            $stmt->execute(array(
                'id' => $id,
            ));
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $fieldValue = $result[$field];
        }

        return $fieldValue;
    }

    public function delete($id, $table)
    {
        $stmt = $this->db->prepare("DELETE FROM $table WHERE id=:id");
        $stmt->execute(array(
            ':id' => $id
        ));

        return $stmt->rowCount() ? true : false;
    }
}
