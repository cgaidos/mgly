<?php

namespace Moowgly\Lib\Utils;

class ElasticSearchLib
{
    /** @var string with json part*/
    private $queryString = '"query" : {"match_all" : {}}';

    /** @var string with json part */
    private $jsonQueryStart = '{';

    /** @var string with json part */
    private $jsonQuerySort;

    /** @var string with json part */
    private $jsonSortQuery;

    /** @var string with json part  Attention je commence par une virgule car il y a toujours un sort avant dans la concaténation*/
    private $jsonQueryBoolStart = '"query": {
                                        "bool": {
                                             "must": [
                                      ';

    /** @var string with json part */
    private $jsonQueryBoolEnd = ']}}';

    /** @var string with json part */
    private $jsonQueryActivity;

    /** @var string with json part */
    private $jsonQueryKeyword;

    /** @var string with json part */
    private $jsonQueryPhoto;

    /** @var string with json part */
    private $jsonQueryRangeDate;

    /** @var string with json part */
    private $jsonQueryGeoloc;

    /** @var string with json part */
    private $jsonQueryEnd = '}';

    /** @var default string, json part */
    private $rangeField = 'date_range';

    /** @var default string, json part */
    private $distance = '20km';

    public function search($elacli, $data, $table)
    {   
        // Remove raw http data
        if(array_key_exists('_RAW_HTTP_DATA', $data)){
            unset($data['_RAW_HTTP_DATA']);
        }

        // $jsonQueryStart = '{';
        // $jsonSortQuery = '"sort" : [
        //                     { "price" : "desc" }
        //                 ],';
        // $jsonQueryBoolStart = '"query": {
        //                             "bool": {
        //                                 "must": [
        //                          ';

        // $jsonQueryBoolEnd = ']}}';

        // $jsonQueryEnd = '}';

        // $jsonQueryActivity = '{
        //                       "term": {
        //                         "activity_code": "pai"
        //                       }
        //                     },';
        // $jsonQueryRangeDate = '{
        //                       "bool": {
        //                         "minimum_should_match": 1,
        //                         "should": [
        //                           {"nested" : {
        //                                 "path" : "calendar_a",
        //                                 "query" : {
        //                                     "bool" : {
        //                                         "must" : [
        //                                           { "range" : {"calendar_a.from" : {"gte" : "2017-03-01"}} },
        //                                           { "range" : {"calendar_a.to" : {"lte" : "2017-06-30"}} }
        //                                         ]
        //                                     }
        //                                 }
        //                             }}
        //                         ]
        //                       }
        //                     },';
        // $jsonQueryGeoloc = '{
        //                       "bool": {
        //                           "filter": {
        //                               "geo_distance" : {
        //                                     "distance" : "100km",
        //                                     "geo_location" : {
        //                                         "lat" : 45.42524119999999,
        //                                         "lon" : 11.912131999999929
        //                                     }
        //                                 }
        //                           }
        //                       }
        //                     }';
        // $jsonQuery =  $jsonQueryStart . $jsonSortQuery . $jsonQueryBoolStart . $jsonQueryActivity . $jsonQueryRangeDate . $jsonQueryGeoloc . $jsonQueryBoolEnd . $jsonQueryEnd;
        
        $query = $this->query($data, $table);

        $params['index'] = 'moowgly';
        $params['type'] = $table;
        $params['body'] = $query;

        $results = $elacli->search($params);

        // $milliseconds = $results['took'];
        // $maxScore     = $results['hits']['max_score'];

        // $score = $results['hits']['hits'][0]['_score'];
        // $doc   = $results['hits']['hits'][0]['_source'];
        // $docs   = $results['hits']['hits'];

        // foreach($docs as $document) { 
        //     echo "<br><br>un seul doc : "; print_r($document["_source"]);
        // }
        // var_dump($results);
        return json_encode($results);

    }

    public static function insert($elacli, $data, $table)
    {
        // Remove raw http data
        if(array_key_exists('_RAW_HTTP_DATA', $data)){
            unset($data['_RAW_HTTP_DATA']);
        }

        // On est obligé d'écrire le tableau de cette façon pour avoir le bon odre des mots clefs

        $params['index'] = 'moowgly';
        $params['type'] = $table;

        if(array_key_exists('id', $data)){
            $params['id'] = $data['id'];
            unset($data['id']);
        }

        $params['body'] = $data;
           
        // Document will be indexed to my_index/my_type/<autogenerated ID>
        $response = $elacli->index($params);

        return json_encode($response);
    }

    public static function update($elacli, $data, $table)
    {
        // Remove raw http data
        if(array_key_exists('_RAW_HTTP_DATA', $data)){
            unset($data['_RAW_HTTP_DATA']);
        }

        $params['index'] = 'moowgly';
        $params['type'] = $table;

        if(array_key_exists('id', $data)){
            $params['id'] = $data['id'];
            unset($data['id']);
        }

        $params['body'] = $data;

        $params['body'] = ['doc' => $data];

        $response = $elacli->update($params);
        var_dump($response);
        return json_encode($response);
    }

    public static function delete($elacli, $data, $table)
    {
        if(array_key_exists('_RAW_HTTP_DATA', $data)){
            unset($data['_RAW_HTTP_DATA']);
        }
        /* il faut récupérer l'id par un search ??*/
        
        $params = [
            'index' => 'moowgly',
            'type' => $table,
            'id' => $data['id']
        ];

        // Delete doc at /my_index/my_type/my_id
        $response = $elacli->delete($params);
    }

    public function query($data, $table)
    {

        if(empty($data)){
            $query =  $this->jsonQueryStart .  $this->jsonSortQuery . ',' . $this->jsonQuery . $this->jsonQueryEnd;

            return $query;
        }

        $this->queryString = '';

        if(!array_key_exists('sort', $data)){

            $data['sort'] = [ "field" => "geo_location", "arrange" => "asc"];
        }

        foreach ($data as $key => $value) {
            
            switch ($key) {

                case 'activity_code':

                   foreach ($value as $activity_code) {
                
                        $this->jsonQueryActivity .= '{
                                                       "term": {
                                                         "activity_code": "' . $activity_code . '"
                                                       }
                                                    },';
                    }
                    
                    // $this->jsonQueryActivity = rtrim($this->jsonQueryActivity, ',');

                    $this->queryString .= $this->jsonQueryActivity;
                   
                    break;

                case 'keyword':
                    
                    $this->jsonQueryKeyword = '{
                                                  "bool": {
                                                    "minimum_should_match": 1,
                                                    "should": [
                                                      {
                                                        "wildcard": {
                                                          "_all": "*' . $value .'*"
                                                        }
                                                      }
                                                    ]
                                                  }
                                                },';

                    $this->queryString .= $this->jsonQueryKeyword;

                    break;

                case 'photo':
                    
                    if($value == '1'){

                        $this->jsonQueryPhoto = '{
                                                    "bool": {
                                                        "must": {
                                                            "exists": {
                                                                "field": "photo"
                                                            }
                                                        }
                                                    }
                                                },';

                        $this->queryString .= $this->jsonQueryPhoto;
                    }

                    break;

                case 'date':
                    

                    if($table == 'Host' ){
                        $this->rangeField = 'calendar_a';
                    }

                    $rangeTo = '';

                    if(isset($value['to'])){

                        $rangeTo = '{ "range" : {"' . $this->rangeField . '.from" : {"lte" : "' . $value['to'] . '"}} },';

                    }

                    $this->jsonQueryRangeDate = '{
                                      "bool": {
                                        "minimum_should_match": 1,
                                        "should": [
                                          {"nested" : {
                                                "path" : "' . $this->rangeField . '",
                                                "query" : {
                                                    "bool" : {
                                                        "must" : [
                                                          ' . $rangeTo . '
                                                          { "range" : {"' . $this->rangeField . '.to" : {"gte" : "' . $value['from'] . '"}} }
                                                        ]
                                                    }
                                                }
                                            }}
                                        ]
                                      }
                                    },';
                    
                    $this->queryString .= $this->jsonQueryRangeDate;

                    break;

                case 'geo_location':
                    
                    if(isset($value['distance'])){
                        
                        $this->distance = $value['distance'];
                    }

                    $this->jsonQueryGeoloc = '{
                                              "bool": {
                                                  "filter": {
                                                      "geo_distance" : {
                                                            "distance" : "' . $this->distance . '",
                                                            "geo_location" : {
                                                                "lat" : ' . $value['lat'] . ',
                                                                "lon" : ' . $value['lon'] . '
                                                            }
                                                        }
                                                  }
                                              }
                                            },'; 

                    $this->queryString .= $this->jsonQueryGeoloc;

                    break;

                case 'sort':

                    switch ($value['field']) {
                        
                        case 'avg_rating':

                            $this->jsonQuerySort = '"sort" : [
                                                        { "avg_rating" : "' . $value['arrange'] . '" }
                                                    ],';

                            break;

                        case 'price':

                            $this->jsonQuerySort = '"sort" : [
                                                        { "price" : "' . $value['arrange'] . '" }
                                                    ],';

                            break;

                        case 'geo_location':

                            $this->jsonQuerySort = '"sort": [
                                                            {
                                                              "_geo_distance": {
                                                                "geo_location": { 
                                                                  "lat":  ' . $data["geo_location"]['lat'] . ',
                                                                  "lon": ' . $data["geo_location"]['lon'] . '
                                                                },
                                                                "order":         "' . $value['arrange'] . '",
                                                                "unit":          "km", 
                                                                "distance_type": "plane" 
                                                              }
                                                            }
                                                          ],';

                            break;
                        
                    }

                    break;
            }
        }

        $this->queryString = rtrim($this->queryString, ',');

        // print_r($this->queryString);

        // exit();

        // if(isset($data['activity_code'])){

        //     foreach ($data['activity_code'] as $activity_code) {
                
        //         $this->jsonQueryActivity .= '{
        //                                        "term": {
        //                                          "activity_code": "' . $activity_code . '"
        //                                        }
        //                                     },';
        //     }
            
        //     $this->jsonQueryActivity = rtrim($this->jsonQueryActivity, ',');
        // }

        // if(isset($data['keyword'])){

        //    $this->jsonQueryKeyword = '{
        //                               "bool": {
        //                                 "minimum_should_match": 1,
        //                                 "should": [
        //                                   {
        //                                     "wildcard": {
        //                                       "_all": "*pain*"
        //                                     }
        //                                   }
        //                                 ]
        //                               }
        //                             }';
        // }

        // if($data['photo'] == '1' ){

        //    $this->jsonQueryPhoto = '{
        //                                 "bool": {
        //                                     "must": {
        //                                         "exists": {
        //                                             "field": "photo"
        //                                         }
        //                                     }
        //                                 }
        //                             }';
        // }

        // if(isset($data['from'])){

        //     $date_to = isset($data['to']) ? $data['to']  : $this->date_to;

        //     if($table == 'Host' ){
        //         $this->rangeField = 'calendar_a';
        //     }

        //     $this->jsonQueryRangeDate = '{
        //                       "bool": {
        //                         "minimum_should_match": 1,
        //                         "should": [
        //                           {"nested" : {
        //                                 "path" : "' . $this->rangeField . '",
        //                                 "query" : {
        //                                     "bool" : {
        //                                         "must" : [
        //                                           { "range" : {"' . $this->rangeField . '.from" : {"lte" : "' . $date_to . '"}} },
        //                                           { "range" : {"' . $this->rangeField . '.to" : {"gte" : "' . $data['from'] . '"}} }
        //                                         ]
        //                                     }
        //                                 }
        //                             }}
        //                         ]
        //                       }
        //                     }';
        // }

        // if(isset($data['geo_location'])){

            
        //     $distance = isset($data['distance']) ? $data['distance']  : $this->distance;
        //     $this->jsonQueryGeoloc = '{
        //                       "bool": {
        //                           "filter": {
        //                               "geo_distance" : {
        //                                     "distance" : "' . $distance . '",
        //                                     "geo_location" : {
        //                                         "lat" : ' . $data['geo_location']['lat'] . ',
        //                                         "lon" : ' . $data['geo_location']['lon'] . '
        //                                     }
        //                                 }
        //                           }
        //                       }
        //                     }'; 
        // }

        // le date range ne marche pas ... voir avec Christos aussi régler le problème des virgules à mettre si il y a différentes champs de recherches

        $query =   $this->jsonQueryStart ;
        $query .=  $this->jsonQuerySort ;
        $query .=  $this->jsonQueryBoolStart ; 
        $query .=  $this->queryString ;
        $query .=  $this->jsonQueryBoolEnd ; 
        $query .=  $this->jsonQueryEnd;

        print_r($query);

        return $query;
    }
}
