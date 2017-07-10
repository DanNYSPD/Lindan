<?php
namespace Lindan\Data;
/**
 * Author: Daniel Jesus Hernandez Francisco 
 * email: daniel.hernandez.fco@gmail.com
 */
include_once("LogerMysqliEcho.php");

class MysqlConnection {

    private $host = null;
    private $user = null;
    private $userPassword = null;
    private $databaseName = null;
    private $mysqli = null;

    const MODE_ASSOC = "MODE_ASSOC";
    const MODE_NUM = "MODE_NUM";

    public function __construct($fromFile = false) {

        if ($fromFile) {
            
        } else {
            $this->host = "localhost";
            $this->user = "root";
            $this->userPassword = "";
            $this->databaseName = "sigap";
        }
    }

    public function connect() {

        $this->mysqli = \mysqli_connect($this->host, $this->user, $this->userPassword, $this->databaseName);
//	echo "vairblae: ". var_dump($conexion);

        if ($this->mysqli->connect_errno) {
            $this->imprimirError($mysqli);
            return false;
        }

        return $this->mysqli;
    }

    //retorna true si todo fue ok
    public function command($sql) {
        $this->connect();
        try {
            $resultado = $this->mysqli->query($sql);
            //echo $sql;
        } catch (Exception $E) {
            throw $E;
        }
        //var_dump($resultado);
        if (!$resultado) {//puede devilver false si el query es incooreco
            $this->imprimirErrorSQL($this->mysqli, $sql);
        }
        $this->close();
        return $resultado;
    }

//consultamos , devuelve el objeto mysqli_result
    private function consultar($sql) {

        if (!is_string($sql) || $sql == "") {
            echo "Error: el query no es string";
            throw new Exception("AcessoBD->realizarConsulta:Falta indicar la consulta");
        }
        if ($this->mysqli->connect_errno) {
            imprimirError($this->mysqli);
            throw new Exception("AcessoBD->realizarConsulta:Falta conexion a la BD");
        }
        //retorna un resultado en ok, si fallo retorna el query retorna false;
        //Retorna FALSE en caso de error. Si una consulta del tipo SELECT, SHOW, DESCRIBE o EXPLAIN es exitosa, mysqli_query() retornará un objeto mysqli_result.
        try {
            $resultado = $this->mysqli->query($sql);
            //echo $sql;
        } catch (Exception $E) {
            throw $E;
        }
        //var_dump($resultado);
        if (!$resultado) {//puede devilver false si el query es incooreco
            $this->imprimirErrorSQL($this->mysqli, $sql);
        }

        return $resultado;
    }

    public function delete($sql) {

        if (!$this->mysqli) {
            throw new Exception("AcessoBD->eliminar:el mysqli fue errorne");
        }
        if (!is_string($sql) || $sql == "") {
            throw new Exception("AcessoBD->eliminar:Falta indicar la consulta");
        }
        if ($this->mysqli->connect_errno) {
            imprimirError($this->mysqli);
            throw new Exception("AcessoBD->eliminar:Falta conexion a la BD");
        }
        try {
            $resultado = $this->mysqli->query($sql);
            //echo $sql;
        } catch (Exception $E) {
            throw $E;
        }
        return $resultado;
    }

    /*     * obtiene el resultado en forma de un array numerico (indice numerico)(numero-valor), donde cada fila(row) es un array numerico tambien (numero-valor)
     * @param  [type]
     * @param  [type]
     * @return [Arreglo numerico con filas en forma de arreglo numerico]
     */

    private function consultarNum($sql) {
        $x = 0;
        $y = 0;
        $arrRes = null;
        $sContenido = "";
        $resultado = $this->consultar($sql);
        if ($resultado) {
            while ($arFila = $resultado->fetch_assoc()) {
                foreach ($arFila as $sContenido) {
                    $arrRes[$x][$y] = $sContenido;
                    $y++;
                }
                $x++;
                $y = 0;
            }
        }
        return $arrRes;
    }

    /* obtiene el resultado en forma de un array numerico(indice numerico: numero-valor),
     *  donde cada fila(row) es un array asociativo (clave-valor)
     * @param  [type]
     * @param  [mysqli object]
     * @return [Arreglo numerico con filas en forma de arreglo asociadtivo]
     */

    private function consultarAssocBeta($sql) {
        $arrRes = null;
        $resultado = $this->consultar($sql);
        if ($resultado) {
            $arrRes = $resultado->fetch_all(MYSQLI_ASSOC);
        }
        return $arrRes;
    }

    public function query($sql, $mode = MysqlConnection::MODE_ASSOC) {
        ///$conexion = conectarBD();
        // mysqli_set_charset($this->mysqli, 'utf8');
        $res=null;
        $this->connect();
        $this->mysqli->set_charset('utf8');
        if ($mode == MysqlConnection::MODE_ASSOC) {
            $res = $this->consultarAssocBeta($sql);
        } else {
            $res = $this->consultarNum($sql);
        }
        if (null == $res){
            return null;
        }
        if (is_array($res) && sizeof($res) == 0) {
            return null;
        }
        $this->close();
        return $res;
    }

    /**
     * @param  [type]
     * @param  [type]
     * @return [type] devuelve UNA SOLA fila del primer resultado (aplica intermente un indice[0])
     */
    public function querySingle($sql) {
        $sql = strtolower($sql);
        $i = 0;
        $arrRes = null;       
        $this->connect();

        $this->mysqli->set_charset('utf8');
        $resultado = $this->consultar($sql, $this->mysqli);
        if ($resultado->num_rows > 0) {
            //var_dump($resultado);
            $arrRes = array();
            while ($arFila = $resultado->fetch_assoc()) {
                if ($i == 1) {//en caso de que el query no inclura in limit con uno basta
                    break;
                    //return $arrRes[0]; //devolvermos la fila
                }
                $arrRes[] = $arFila;
                $i++;
            }
            $this->close();
            return $arrRes[0];
        } else {
            $this->close();
            return null;
        }
    }

    public function prepare($sql) {
        if ($this->mysqli) {
            return $this->mysqli->prepare($sql);
        } else {
            $this->connect();
            return $this->mysqli->prepare($sql);
        }
    }

    public function close() {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }

    private function imprimirError($mysqli) {
        $Ilogger = new LoggerMysqliEcho();
        $Ilogger->imprimirError($mysqli);
    }

    private function imprimirErrorSQL($mysqli, $sql) {
        $Ilogger = new LoggerMysqliEcho();
        $Ilogger->imprimirErrorSQL($mysqli, $sql);
    }

}

?>