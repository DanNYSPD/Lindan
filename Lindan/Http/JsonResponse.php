<?php

namespace Lindan\Http;

include_once('Response.php');

class JsonResponse extends Response {

    function __construct($code = 404) {
        $this->setResponseCode($code);
    }

    public function jsonContentType($value = '') {
        header("Content-type:application/json");
    }

    public function response($value = null) {
        if ($value != null) {

            $this->jsonContentType();
            http_response_code(200);
            echo json_encode($value);
        } else {
            //http_response_code(404);

            if ($this->getResponseCode() == -1) {
                //$this->jsonContentType();
                echo $this->getMessage();
                http_response_code(404);
            } else {
                $this->jsonContentType();
                http_response_code($this->getResponseCode());
            }
        }
    }

    function errorResponse($msj, $code = 404) {
        if(is_array($msj)){
            echo json_encode($msj);
            http_response_code($code);
            die();
        }else{
            echo json_encode(array("msj"=>$msj));
            http_response_code($code);
            die();
        }
    }

}

?>