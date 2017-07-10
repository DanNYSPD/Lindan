<?php

namespace Lindan\Http;

/**
 * Description of Request
 *
 * @author daniel
 */
class Request {

    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PURGE = 'PURGE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';
    const DEFAULT_VALUE = "";

    public function __construct() {
        
    }

    public function GET($key) {
        //No warning is generated if the variable does not exist. That means empty() is essentially the concise equivalent to !isset($var) || $var 
        return (isset($_GET($key))) ? $_GET[$key] : DEFAULT_VALUE;
        /*

         * "" (an empty string)
          0 (0 as an integer)
          0.0 (0 as a float)
          "0" (0 as a string)
          NULL
          FALSE
          array() (an empty array)
          $var; (a variable declared, but without a value) 
         */
    }
    public function POST($key) {
        return (isset($_POST($key))) ? $_POST[$key] : DEFAULT_VALUE;
    }
    /*
    public function createFromGlobals($param) {
        createRequestFromFactory($_GET,$_POST,array(),$_COOKIE,$_FILES,$_SERVER)
    }
     * 
     
    function createRequestFromFactory($arrGET,$arrPOST,array(),$_COOKIE,$_FILES,$server) {
        
    }
     * 
     */
}
