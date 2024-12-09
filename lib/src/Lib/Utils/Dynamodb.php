<?php

namespace Moowgly\Lib\Utils;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

class Dynamodb
{

    public static function retrieve($ddbc, $keys, $table)
    {
        // Remove raw http data
        if(array_key_exists('_RAW_HTTP_DATA', $keys)){
            unset($keys['_RAW_HTTP_DATA']);
        }

        // Remove elastic data
        if(array_key_exists('elasticData', $keys)){
            unset($keys['elasticData']);
        }

        $arrayReq = array();

        $marshaler = new Marshaler();

        $itemJson = json_encode($keys);

        $arrayReq['TableName'] = $table;
        $arrayReq['Key'] = $marshaler->marshalJson($itemJson);

        $response = $ddbc->getItem($arrayReq);

        $item = $marshaler->unmarshalItem($response['Item']);

        return json_encode($item);
    }

    public static function insert($ddbc, $data, $table)
    {
        // Remove raw http data
        if(array_key_exists('_RAW_HTTP_DATA', $data)){
            unset($data['_RAW_HTTP_DATA']);
        }

        // Remove elastic data
        if(array_key_exists('elasticData', $data)){
            unset($data['elasticData']);
        }

        $data = self::cleanData($data);

        $marshaler = new Marshaler();

        $itemJson = json_encode($data);

        $response = $ddbc->putItem([
            'TableName' => $table,
            'Item'      => $marshaler->marshalJson($itemJson)
            // "ConditionExpression" => "attribute_not_exists(id)"
        ]);

        return json_encode($response['@metadata']['statusCode']);
    }

    public static function update($ddbc, $data, $table)
    {
        // Remove raw http data
        if(array_key_exists('_RAW_HTTP_DATA', $data)){
            unset($data['_RAW_HTTP_DATA']);
        }

        // Remove elastic data
        if(array_key_exists('elasticData', $data)){
            unset($data['elasticData']);
        }

        // Remove key name in data or there will be a conflict in dynamodb
        // unset($data['id' . '_' . $table ]);

        $marshaler = new Marshaler();

        $keysJson = json_encode($data['keys']);

        $keysArray = $marshaler->marshalJson($keysJson);

        unset($data['keys']);

        $itemJson = json_encode($data);

        $itemArray = $marshaler->marshalJson($itemJson);

        $arrayReq = array();

        $arrayReq['TableName'] = $table;
        $arrayReq['Key'] = $keysArray;

        $arrayReq['ExpressionAttributeValues'] = "";
        $set = " set";
        $remove = " remove";
        $arrayReq['UpdateExpression'] = "";


        foreach ($itemArray as $attr => $value) {

            if($value == array('S' => '#dtdm' )){

                $remove .= " " . $attr . ",";

            }else{
                $arrayReq['ExpressionAttributeValues'][':' . $attr] = $value;

                $set .= " " . $attr . " = :" . $attr . ",";
            }

        }

        if($arrayReq['ExpressionAttributeValues'] == ""){
           unset($arrayReq['ExpressionAttributeValues']);
        }

        $set = rtrim($set, ",");
        $remove = rtrim($remove, ",");

        if($set != " set"){
            $arrayReq['UpdateExpression'] .= $set;
        }

        if($remove != " remove"){
            $arrayReq['UpdateExpression'] .= $remove;
        }

        // $arrayReq['ReturnValues'] = 'ALL_NEW';

        $response = $ddbc->updateItem($arrayReq);

        return json_encode($response['@metadata']['statusCode']);
    }

    public static function delete($ddbc, $data, $table)
    {

        // Remove raw http data
        if(array_key_exists('_RAW_HTTP_DATA', $data)){
            unset($data['_RAW_HTTP_DATA']);
        }

        // Remove elastic data
        if(array_key_exists('elasticData', $data)){
            unset($data['elasticData']);
        }


        $marshaler = new Marshaler();

        $keysJson = json_encode($data['keys']);

        $keysArray = $marshaler->marshalJson($keysJson);

        unset($data['keys']);

        $itemJson = json_encode($data);

        $arrayReq = array();

        $arrayReq['TableName'] = $table;
        $arrayReq['Key'] = $keysArray;

        $arrayReq['ExpressionAttributeValues'] = [':bool' => ['BOOL' => TRUE]];

        $arrayReq['UpdateExpression'] = 'set deleted = :bool';
        // $arrayReq['ReturnValues'] = 'ALL_NEW';

        $response = $ddbc->updateItem($arrayReq);

        return json_encode($response['@metadata']['statusCode']);
    }

    public static function cleanData($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::cleanData($data[$key]);
            }

            if (empty($data[$key])) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}