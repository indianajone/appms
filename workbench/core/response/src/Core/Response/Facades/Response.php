<?php

namespace Core\Response\Facades;

use Illuminate\Support\Facades\Response as BaseResponse;
use Illuminate\Support\Facades\DB;

class Response extends BaseResponse {

    public $response;

    /**
     *
     * Return json format
     * @param array $status
     * @param mix $data
     * @param int $offset
     * @param int $limit
     * @return JsonResponse
     *
     * */
    public static function listing($status, $data, $offset = 0, $limit = 10 ,$format = 'json') {
        if(is_object($data)) {
            $total = $data->count();
        } else if (is_array($data)) {
            $total = count($data);
        } else {
            $total = 0;
        }
        
        $response = array(
            'header' => array(
                'code' => $status['code'],
                'message' => $status['message']
            ),
            'offset' => $offset,
            'limit' => $limit,
            'total' => $total,
            'entries' => is_object($data) ? $data->toArray() : $data
        );
        
        return self::result($response, $format);
    }

    public static function result($response , $format) {
        if($format == 'json') {
            return Response::make($response)->header('Content-Type', 'text/json');
        } else {
            return Response::make(self::xml($response))->header('Content-Type', 'text/xml');
        }
    }

    /**
     *
     * Return xml format
     * @return xml response
     *
     * */
    public static function xmlEncode($_str) {
        $_tmp = str_replace("&", "&amp;", $_str);
        $_tmp = str_replace('<', '&lt;', $_tmp);
        $_tmp = str_replace(">", "&gt;", $_tmp);
        $_tmp = str_replace('\"', "&quot;", $_tmp);
        $_tmp = str_replace("\'", "&apos;", $_tmp);

        return $_tmp;
    }

    public static function isAssoc($array) {
        return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }

    public static function xml($data, $rootNodeName = 'ResultSet', &$xml = null) {
        if (is_array($data)) {
            if (ini_get('zend.ze1_compatibility_mode') == 1) {
                ini_set('zend.ze1_compatibility_mode', 0);
            }
            if (is_null($xml)) { //$xml = simplexml_load_string( "" );
                $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
            }

            foreach ($data as $key => $value) {
                $numeric = false;
                if (is_numeric($key)) {
                    $numeric = 1;
                    $key = $rootNodeName;
                }

                $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);
                if (is_array($value)) {
                    $node = Response::isAssoc($value) || $numeric ? $xml->addChild($key) : $xml;
                    if ($numeric) {
                        $key = 'anon';
                    }
                    Response::xml($value, $key, $node);
                } else {
                    $value = Response::xmlEncode($value);
                    if (substr($key, 0, 2) == "A_") {
                        $xml->addAttribute(substr($key, 2), $value);
                    } else {
                        $xml->addChild($key, $value);
                    }
                }
            }
            //header("Content-type: text/xml");
            return $xml->asXML();
        } else {
            return "Is not array";
        }
    }

    public static function json_to_xml($json, $rootNodeName = 'ResultSet') {
        $object = json_decode($json);
        $array = json_decode(json_encode($object), true);
        $result = Response::array_to_xml($array, $rootNodeName);
        return $result;
    }

    public static function message($status, $format = 'json') {
        if (is_array($status)) {
            $response = array(
                'header' => array(
                    'code' => $status['code'],
                    'message' => $status['message']
                )
            );

            return self::result($response, $format);
        } else {
            return "Please set status code ans message.";
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public static function getAllColumnsNames($table) {
        $query = 'SHOW COLUMNS FROM ' . $table;
        $column_name = 'Field';
        $column_type = 'Type';
        $columns = array();

        foreach (DB::select($query) as $column) {
            array_push($columns, array($column->$column_name => $column->$column_type));
        }

        return $columns;
    }
    
    public static function fields($table, $format = 'json') {
        $display = array();
        $column = Response::getAllColumnsNames($table);

        foreach($column as $data) {
            foreach($data as $subkey => $subdata) {
                $display[$subkey] = $subdata;
            }
        }

        $response = array(
            'header' => array(
                'code' => 200,
                'message' => 'success'
            ),
            'fields' => $display
        );

        return self::result($response, $format);
    }
}
