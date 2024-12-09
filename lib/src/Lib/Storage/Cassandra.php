<?php

namespace Moowgly\Lib\Storage;

use evseevnn\Cassandra\Database;

/**
 * How to use Cassandra DB connection
 * new Cassandra(
 *      "test_keyspace",
 *      ['127.0.0.1:8888' => [
 *          'username' => 'admin',
 *          'password' => 'pass'
 *      ]]);.
 */
class Cassandra extends Storage
{
    /**
     * Constructor.
     *
     * @param string $keyspace
     *                         : keyspace name.
     * @param array  $host
     *                         : Array containing Cassandra cluster servers.
     */
    public function __construct($keyspace, $host)
    {
        $database = new Database($host, $keyspace);
        $database->connect();
        $this->db = $database;
        self::$_instance = $this;
    }

    public function get($id, $table)
    {
        try {
        	if($id == ''){
	            $result = $this->db->query("SELECT * FROM " . $table . " WHERE deleted = 'false'");
        	}
        	else{
	            $result = $this->db->query('SELECT * FROM ' . $table . ' WHERE id = :id  AND deleted = :deleted', [
	                'id' => $id,
	                'deleted' => 'false'
	            ]);
        	}

            if (count($result) == 1) {
                $result = $result[0];
                $result['contentType'] = $result['contenttype'];
                $result['dateUpdate'] = $result['dateupdate'];

                return $result;
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function post($id, $md5, $dateUpdate, $contentType, $data, $table, $indexed = false)
    {
        try {
            $this->db->query('INSERT INTO "' . $table . '" ("id", "md5", "dateupdate", "contenttype", "deleted", "indexed", "data") VALUES (:id, :md5, :dateUpdate, :contentType, :deleted, :indexed, :data);', [
                'id' => $id,
                'md5' => $md5,
                'dateUpdate' => $dateUpdate,
                'contentType' => $contentType,
                'deleted' => 'false',
                'indexed' => $indexed ? 'true' : 'false',
                'data' => $data
            ]);

            return true;
        } catch (\Exception $e) {
            error_log($e);

            return false;
        }
    }

    public function put($id, $oldMd5, $md5, $dateUpdate, $data, $table, $indexed = false)
    {
        $result = $this->db->query("UPDATE $table SET md5 = :md5, dateupdate = :dateUpdate, indexed = :indexed, data = :data WHERE id = :id ", [
            'id' => $id,
            'md5' => $md5,
            'dateUpdate' => $dateUpdate,
            'indexed' => $indexed,
            'data' => $data
        ]);
        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id, $table, $indexed = false)
    {
        $result = $this->db->query("UPDATE $table SET deleted = 'true', indexed = :indexed WHERE id = :id", [
            'id' => $id,
            'indexed' => $indexed ? 'true' : 'false'
        ]);
        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getField($id, $table, $field)
    {
        try {
            $result = $this->db->query('SELECT '.$field.' FROM "'.$table.'" WHERE "id" = :id', [
                'id' => $id,
            ]);
            if (count($result) == 1) {
                $result = $result[0][$field];

                return $result;
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function create($table)
    {
        $req = "CREATE TABLE $table (
    	id varchar PRIMARY KEY,
    	md5 varchar,
    	dateupdate varchar,
    	contenttype varchar,
    	deleted varchar,
    	indexed varchar,
    	data varchar
    	);";

        $this->db->query($req);

        $this->db->query("CREATE INDEX ON $table (deleted);");
        $this->db->query("CREATE INDEX ON $table (indexed);");
    }

    public function drop($table)
    {
        $req = "DROP TABLE IF EXISTS $table;";
        $this->db->query($req);
    }

    public function beginMultiRequest()
    {
        $this->db->beginBatch();
    }

    public function endMultiRequest()
    {
        $this->db->applyBatch();
    }

    /**
     * Get datas from stock
     * @param unknown $parameters
     */
    public function getStock($parameters){
    	
        $sku_number = isset($parameters['sku_number']) ? $parameters['sku_number'] : null;
        $style_number = isset($parameters['style_number']) ? $parameters['style_number'] : null;
        $store_name = isset($parameters['store_name']) ? $parameters['store_name'] : null;
        $product_size = isset($parameters['product_size']) ? $parameters['product_size'] : null;
    	
        $result = null;
        $where = "";
			
        //Construct SQL request
        $request = "SELECT * FROM stock WHERE";
        
        $where = !empty($sku_number) ? ($where != "" ? " AND" : "") . " sku_number = '$sku_number'" : "";
        $where .= !empty($style_number) ? ($where != "" ? " AND" : "") . " style_number = '$style_number'" : "";
        $where .= !empty($store_name) ? ($where != "" ? " AND" : "") . " store_name = '$store_name'" : "";
        $where .= !empty($product_size) ? ($where != "" ? " AND" : "") . " size = '$product_size'" : "";
        
        $request .= $where . ' order by store_name';
		$stmt = $this->db->prepare($request);
	    $stmt->execute();
	    $result = $stmt->fetchAll();
        
        return $result;
        
    }
}
