<?php

namespace Moowgly\Lib\Storage;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Elasticsearch;
use Moowgly\Lib\Utils\ElasticSearchLib;
use Moowgly\Lib\Utils\Dynamodb;

class ElasticDynamo extends Storage
{
    private $ddbc; // DynamoDbClient var
    private $elacli; // ElasticsearchClient var
    /**
     * Constructor.
     */
    public function __construct($region, $version, $key, $secret)
    {
        $this->ddbc = DynamoDbClient::factory(array(
                        'endpoint'   => 'http://localhost:8000',  // Use DynamoDB running locally
                        'region'   => $region,
                        'version'  => $version,
                        'credentials' => array(
                            'key' => $key,
                            'secret'  => $secret,
                        )
                    ));

        $this->elacli = Elasticsearch\ClientBuilder::create()->build();      

        self::$_instance = $this;
    }

    public function get($data, $table, $query = false)
    {

        if($query == false){
            
            $dynamoDb = Dynamodb::retrieve($this->ddbc, $data, $table);
            
            return $dynamoDb;
        
        }else{

            $elasticSearch = new ElasticSearchLib;
            $elasticSearch = $elasticSearch->search($this->elacli, $data, $table);

            return $elasticSearch;
        }

    }

    public function post($data, $table)
    {
        if($table == "Guest"){
            $dynamoDb = Dynamodb::insert($this->ddbc, $data, $table);
        }else{

            $dynamoDb = Dynamodb::insert($this->ddbc, $data, $table);

            if($dynamoDb == '200'){
                $elasticSearch = ElasticSearchLib::insert($this->elacli, $data['elasticData'], $table);
            }

        }
    }

    public function put($data, $table)
    {
        if($table == "Guest"){
            $dynamoDb = Dynamodb::update($this->ddbc, $data, $table);
        }else{

            $dynamoDb = Dynamodb::update($this->ddbc, $data, $table);

            if($dynamoDb == '200'){
                $elasticSearch = ElasticSearchLib::update($this->elacli, $data['elasticData'], $table);
            }
        }
    }

    public function delete($data, $table)
    {
        if($table == "Guest"){
            $dynamoDb = Dynamodb::delete($this->ddbc, $data, $table);
        }else{

            $elasticSearch = ElasticSearchLib::delete($this->elacli, $data['elasticData'], $table);

            $dynamoDb = Dynamodb::delete($this->ddbc, $data, $table);
        }
    }

    public function getField($id, $table, $field = 'md5')
    {
        $fieldValue = null;
        if ($id) {
            $stmt = $this->db->prepare("SELECT $field FROM $table WHERE id=:id");
            $stmt->execute(array(
                'id' => $id,
            ));
            $result = $stmt->fetch();
            $fieldValue = $result[$field];
        }

        return $fieldValue;
    }
}
