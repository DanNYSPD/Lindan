<?php

namespace Lindan\Session;

/**
 * Clase basica para controlar sessiones, se puede extender o usar por composicion para extender y personalizar
 * funcionalidad
 *
 * @author daniel
 */
class Session {

    private $userId = "";
    private $userPass = "";
    private $userRol = "";

    const USER_ROL = "USER_ROL";
    const USER_ID = "USER_ID";
    const USER_NAME = "USER_NAME";
    const USER_PASS = "USER_PASS";

    function __construct($start_Session = true) {
        /*
          if ($start_Session) {
          session_start();
          }
         * *
         */
    }

    /**
     * [Debe iniciarse manualmente]
     * @return [type] [description]
     */
    public function start() {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    /**
     *  obttiene (si existe) el valor de la session con la jey
     * @param string $key
     * @return void
     */
    public function get($key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }

    /**
     * Setea los atributos de la session
     * @param type $key
     * @param type $value
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Verifica si el existe el id de usuario en la sesion, el id puede ser un numero de control
     * @return boolean
     */
    function IsAuthenticatedUserWithId() {
        if (!isset($_SESSION[USER_ID])) {
            return false;
        }
    }

    function IsAuthenticatedUserWithName() {
        if (!isset($_SESSION[USER_NAME])) {
            return false;
        }
    }

    public function IsAuthenticated() {
        
    }

    public function IsRole($userRole) {
        if (isset($_SESSION[USER_ROLE])) {
            if ($_SESSION[USER_ROLE] == $userRole) {
                return true;
            }
        }
        return false;
    }

    public function startSession($userId = "", $userName = "", $userRole = "", $userPass = "") {
        $_SESSION[USER_ID] = $userId;
        $_SESSION[USER_NAME] = $userName;
        $_SESSION[USER_ROL] = $userRole;
        $_SESSION[USER_PASS] = $userPass;
    }

    public function getUserName() {
        if (isset($_SESSION[USER_NAME])) {
            return $_SESSION[USER_NAME];
        }
        return null;
    }

    public function getUserId() {
        if (isset($_SESSION[USER_ID])) {
            return $_SESSION[USER_ID];
        }
        return null;
    }

    public function closeSession() {
        //libera la sesión actual, elimina cualquier dato de la sesión.
        session_destroy();

        unset($_SESSION[USER_ID]);
        unset($_SESSION[USER_ROL]);
        unset($_SESSION[USER_PASS]);

        //libera la sesion
        session_unset();
    }

}

?>
