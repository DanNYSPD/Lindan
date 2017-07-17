<?php

namespace Lindan\Data;
use PDO;
/**
 * Description of PdoPgConnection
 *
 * @author daniel
 */
class PdoPgConnection {
    private $port = '5432'; // para posgtes este es por defecto
    private $host = 'localhost'; //por defecto
    private $userName = 'dan';
    private $databaseName = 'bdescuela';
    private $userPassword = '12345';
    private $charset="utf8"; // asi es utf8 para pdo
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
            echo "\n error:[ ".$error."]\n";
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
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            //$dsn="pgsql:dbname=$this->databaseName ;host= $this->host ;port=$this->port; charset=$this->charset ;";
            $dsn="pgsql:dbname=$this->databaseName ;host= $this->host ;port=$this->port;";
            $this->connection = new  \PDO(
                $dsn,
                $this->userName, 
                $this->userPassword,
                $opt);
        } catch (\PDOException  $e) {
            $this->setError($e->getMessage());
        }        
        return $this->connection;
    }
    /**
     * 
     * @param type $sql
     * @param type $params
     * @return mixed true en caso de exito, false en caso de fallo, PDOobjet en caso de error.
     * @throws \Exceptio
     */
    public function commandParams($sql,$params= array()) {
        $this->connect();
          if (trim($sql) == '') {
            throw new \Exception("PgConnection->command:Falta indicar el comando");
        }
        if (!$this->connection) {
            throw new \Exception("PgConnection->falta conexion bd");
        }
        $res=null;
        try {
            $stmt=$res = $this->connection->prepare($sql);
            // preparedStatament->excute() Devuelve TRUE en caso de éxito o FALSE en caso de error. 
            $res=$stmt->execute($params);
            
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
        }
        return $res;
    }
    /**
     * rescata el primer elemento de una consulta, el usuarui es espondesable delq ue la consulta devuelva un solo resultado
     * como limit 1; 
     * @param type $sql
     * @param type $params
     * @return type
     */
    public function querySingleParams($sql,$params=array()){
        $res= $this->queryParams($sql, $params);
         if($res && is_array($res) && sizeof($res)>0){
            return $res[0];
        }else{
            return null;
        }       
    }
    /**
     * Metodo que se encarga de realizar una consulta, no es necesario usar escape-.string
     * @param string $sql  es el sql a realizar, se usan placeholders con nombres ; "select * from tabla where id=:id"
     * o con ? : "select * from tabla where id=?", de esta cosulta dependera el tipo de array en el parametro.
     * para nombres, se requiren obviamente que sea asociativo, y si es con ?, seria por el ordende los parametros del array
     * @param array $params es el array de parametros a hacer binding, pueder ser asociavo: array("id"=>1) , 
     * puede ser por orden
     * @return mixed , retorna un array con los resultados o null si no se encontro ningun registro
     */
    public function queryParams($sql,$params= array()){
        //echo "queryParams";
        $this->connect();
        //echo "aqui--";
        $this->checkSqlAndConnection($sql);
        if($this->getError()){
            return null;
        }
       // echo "aqui";
        $res=null;
        try {
            $stmt=$res = $this->connection->prepare($sql);
            $stmt->execute($params);
            $res=$stmt->fetchAll();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
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
        if ($res) {           
            if (is_array($res) && sizeof($res) == 0) {
                return null;
            }
        } else {           
            return $res;
        }
        if ($this->auto) {
            $this->close();
        }
        return $res;      
    }
    private function checkSqlAndConnection($sql){        
        if ($sql == "") {
            $this->setError("AcessoBD->realizarConsulta:Falta indicar la consulta");
            
        }
        if (!$this->connection) {
            $this->setError("AcessoBD->realizarConsulta:Falta conexion a la BD");
            
        }
        if(!$this->connection instanceof \PDO){
            $this->setError("No es un objeto pdo");
        } 
        $this->setError(null);
    }

   

    public function close() {
        //de acuerdo a la documentacion para la coneccion se mantiene activa por el tiempo de vuda del objeto pdo
        $this->connection=null;
    }
}
