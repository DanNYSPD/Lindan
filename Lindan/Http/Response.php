<?php

namespace Lindan\Http;

class Response {

    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_FOUND=302;
    const HTTP_BAD_REQUEST = 400;// General error when fulfilling the request would cause an invalid state.Domain validation errors, missing data, etc. are some examples.
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_SERVICE_UNAVAILABLE = 503;

    protected $code = -1;
    protected $message = "";

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message = '') {
        $this->message = $message;
    }

    function __construct($code = 200) {
        $this->code = $code;
    }

    public function setResponseCode($code = 200) {
        $this->code = $code;
    }

    public function getResponseCode() {
        return $this->code;
    }

    public function response($value = null) {



        http_response_code($this->getResponseCode());
    }

}

?>