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
    private $method="";
    public function __construct() {
        switch ($_SERVER['REQUEST_METHOD']) {
          case METHOD_GET:
             $this->method=METHOD_GET;
            break;
          case METHOD_POST:
            $this->method=METHOD_POST;
            break;
          case METHOD_PUT:
            $this->method=METHOD_PUT;
            break;
          case METHOD_PATCH:
            $this->method=METHOD_PATCH;
            break;
          case METHOD_DELETE:
            $this->method=METHOD_DELETE;
            break;    

          default:
            # code...
            break;
        }
    }
     public function isGET($value='')
    {
      return (METHOD_GET===$_SERVER['REQUEST_METHOD']);
    }
    public function isPOST($value='')
    {
      return (METHOD_POST===$_SERVER['REQUEST_METHOD']);
    }
    public function getAttribute($key='')
    {
      switch ($this->method) {
        case METHOD_GET:
          return (isset($_GET[$key]))?$_GET[$key]:null;
        case METHOD_POST:
          return (isset($_GET[$key]))?$_GET[$key]:null;
        case METHOD_PUT:
          return (isset($_GET[$key]))?$_GET[$key]:null;
        case METHOD_PATCH:
          return (isset($_GET[$key]))?$_GET[$key]:null;
        case METHOD_DELETE:
          return (isset($_GET[$key]))?$_GET[$key]:null;
        default:
          # code...
          break;
      }
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
