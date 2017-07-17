<?php

namespace Lindan\Data;

include_once 'IDataBaseConnection.php';
/**
 * @author Daniel j Hernandez Fco, email: @daniel.hernandez.fco@gmail.com
 */
class PgConnection {

    private $port = '5432'; // para posgtes este es por defecto
    private $host = 'localhost'; //por defecto
    private $userName = 'dan';
    private $databaseName = 'bdescuela';
    private $userPassword = '12345';

    const database_driver = 'database_driver'; //para clase generica
    const database_host = 'database_host';
    const database_name = 'database_name';
    const database_user = 'database_user';
    const database_password = 'database_password';
    const charset = 'charset';  //por defecto en esta version ser utf-8

    
    private $auto=true;
    /**
     * variable que contiene el ultimo error tras una operacion
     * @var string   
     */
    private $error=null;
    
    public function getError(){
        return $this->error;
    }
    public function setError($error){
        if($error){
            echo "\n error:".$error;
        }
        $this->error=$error;              
    }
    /**
     * objeto Ilooger para regstrar alertas, informacion, errores,etc
     * @var null
     */

    private $connection = -1;
    private $ILogger = null;

    private function setLogger($Ilogger) {
        $this->Ilogger = $Ilogger;
    }

    private function setDataSource($dataSource) {
        //si es un array extraemos los datos:
        if (is_array($dataSource)) {
            $this->databaseName = $dataSource[database_name];
            $this->host = $DataSource[database_host];
            $this->userName = $dataSource[database_user];
            $this->userPassword = $dataSource[database_password];
            //$this->port=$dataSource[database_port];
            //si es una instancia de iDataSource extreemos los datos
        } else if (is_object($DataSource) && ( $dataSource instanceof IDataSource)) {
            
        }
    }

    public function __construct($fromFile = false) {
        
    }

    public function connect() {
        $conectExitosa = false;
        try {

            $this->connection = \pg_connect("
                host=" . $this->host . "
                port=" . $this->port . "
                user=" . $this->userName . " 
                password=" . $this->userPassword . "
                dbname=" . $this->databaseName . "
                ", PGSQL_CONNECT_FORCE_NEW);
        } catch (Exception $E) {
            // throw ($E);
            //  echo "catch";
        }
        if (!$this->connection) {
            //throw new \Exception("Error al conectar");
        } else {
            //$this->connection = 1;
            $conectExitosa = true;
        }
        return $conectExitosa;
    }
    public function commandParams($sql,$params= array()) {
        $this->connect();
          if (trim($sql) == '') {
            throw new \Exception("PgConnection->command:Falta indicar el comando");
        }
        if (!$this->connection) {
            throw new \Exception("PgConnection->falta conexion bd");
        }
         
         return $res = pg_query_params($this->connection, $sql,$params);
    }
    //retorna true si todo fue ok
    public function command($sql) {
        $this->connect();
          if (trim($sql) == '') {
            throw new \Exception("PgConnection->command:Falta indicar el comando");
        }
        if (!$this->connection) {
            throw new \Exception("PgConnection->falta conexion bd");
        }
         
         return $res =  $res = pg_query_params($this->connection, $sql,$params);
    }
     /**
     * @param  [type]
     * @param  [type]
     * @return [type] devuelve UNA SOLA fila del primer resultado (aplica intermente un array[0])
     */
    public function querySingle($sql) {
        $res=$this->query($sql);
        if($res && is_array($res) && sizeof($res)>0){
            return $res[0];
        }
    }
    public function querySingleParams($sql,$params=array()){
        $res= $this->queryParams($sql, $params);
         if($res && is_array($res) && sizeof($res)>0){
            return $res[0];
        }       
    }
    /**
     * Metodo que se encarga de realizar una consulta, no es necesario usar escape-.string
     * @param string $sql  es el sql a realizar
     * @param array $params es el array de parametros a hacer binding
     * @return mixed , retorna un array con los resultados o null si no se encontro ningun registro
     */
    public function queryParams($sql,$params= array()){
        $this->connect();
        $this->checkSqlAndConnection($sql);
        if($this->getError()){
            return null;
        }
        
        $res=null;
        try {
            $res = pg_query_params($this->connection, $sql,$params);
            //die("ñal");
        } catch (Exception $E) {
        }
        return $this->tratarResultQuery($res);        
    }
    /**
     * metodo interno que se encarga de controlar el resultado de una consulta
     * creado ya que query y queryParams tienen y deberan tener la misma logica de resulado
     * @param type $res
     * @return type+
     */
    private function tratarResultQuery($res){
        $arrRes=null;
        if ($res) {           
            if (is_array($res) && sizeof($res) == 0) {
                return null;
            }           
            $arrRes = $this->resultCollectorAsociative($res);
            pg_free_result($res);
        } else {           
            return $res;
        }
        if ($this->auto) {
            $this->close();
        }
        return $arrRes;      
    }
    private function checkSqlAndConnection($sql){
        
        if ($sql == "") {
            $this->setError("AcessoBD->realizarConsulta:Falta indicar la consulta");
            
        }
        if (!$this->connection) {
            $this->setError("AcessoBD->realizarConsulta:Falta conexion a la BD");
            
        }
        $this->setError(null);
    }

    public function query($sql='') {
        if ($this->auto) {
            $this->connect();
        }
        $res = null;
        if ($sql == "") {
            throw new \Exception("AcessoBD->realizarConsulta:Falta indicar la consulta");
        }
        if (!$this->connection) {
            throw new \Exception("AcessoBD->realizarConsulta:Falta conexion a la BD");
        }
        // var_dump($this->connection);
        try {
            $res = pg_query($this->connection, $sql);
        } catch (Exception $E) {
            //throw $E;
        }
        return $this->tratarResultQuery($res);
    }
    private function  resultCollectorAsociative($res){
        $arrRes=null;
        while ($arFila = pg_fetch_assoc($res)) {            
            $arrRes[] = $arFila;
        }
        return $arrRes;
    }

    private function resultCollectorNumeric($res) {
        $x = 0;
        $y = 0;
        $arrRes = null;
        $sContenido = "";
        while ($arFila = pg_fetch_row($res)) {
            foreach ($arFila as $sContenido) {
                $arrRes[$x][$y] = $sContenido;
                $y++;
            }
            $x++;
            $y = 0;
        }
        return $arrRes;
    }

   

    public function close() {
        $conectExitosa = false;
        if ($this->connection) {
            $conectExitosa = pg_close($this->connection);
        }
        return $conectExitosa;
    }
  
}