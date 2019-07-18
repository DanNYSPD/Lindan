<?php
declare (strict_types = 1);
namespace Lindan\Data;
//https://www.postgresql.org/docs/9.4/static/errcodes-appendix.html
//use Lindan\Logger;
use PDO;

/**
 * Clase Wrapper PDO para postgres
 * Nota. Todas mis funciones son de carga Lazi , es decir, que se conecta (abre la conexion)a  la bd solo cuando se ejecuta una funcion.
 * @author Daniel  <daniel.hernandez@gmail.com>
 */
class PdoPgConnection
{
    private $port = '5432'; // para posgtes este es por defecto
    private $host = 'localhost'; //por defecto
    private $userName = '';
    private $databaseName = '';
    private $userPassword = ''; //usar el constructor para asignar este valor
    private $charset = "utf8"; // asi es utf8 para pdo

    private $databaseType='';//pordefecto sera postgres
    const database_driver = 'database_driver'; //para clase generica(usar database Type)
    const database_host = 'database_host';
    const database_name = 'database_name';
    const database_user = 'database_user';
    const database_password = 'database_password';
    const database_type = 'database_type';
    const database_port='database_port';
    const charset = 'charset'; //por defecto en esta version ser utf-8

    private $production = true;
    /**
     * Indica si se debe cerrar la conexion tras una consulta
     *
     * @var boolean
     */
    private $auto = false;
    /**
     * variable que contiene el ultimo error tras una operacion
     * @var string
     */
    private $error = null;

    public function getError()
    {
        return $this->error;
    }
    /**
     * Wraps the error_log function so that this can be easily tested
     *
     * @param $message
     */
    protected function logError($message)
    {
        if (\is_string($message)) {
            error_log($message);
        } else if (is_object($message)) {
            if ($message instanceof \Exception || $message instanceof \Error) {
                $strError = "Error in PDOConnection class ";
                // $strError.=   \json_encode((array)$message);
                $strError .= LoggerPDO::renderExceptionOrError($message);

                while ($message = $message->getPrevious()) {
                    $strError .= '<h2>Previous exception</h2>';
                    $strError .= LoggerPDO::renderExceptionOrError($message);
                }
                \error_log($strError);
            }
        } else {
            error_log("error en logError , message no es string ni tampoco Exception or Error ");
        }
    }
    public function setError($error)
    {
        $this->error = $error;
        if (!$error) {
            return;
        }

        if ($this->production) {
            $this->logError($error);
        } else {
            echo "\n error en PdoPgConnection:[ " . $error . "]\n";
        }

    }
    /**
     * Establece el nombre de la base de datos de la conexion
     *
     * @param string $databaseName
     * @return void
     */
    public function setDataBaseName(string $databaseName)
    {
        $this->databaseName = $databaseName;
    }

    /**
     * Objecto PDO
     *
     * @var \PDO
     */
    private $connection = null;

    /**
     * objeto Ilooger para regstrar alertas, informacion, errores,etc
     * @var null
     */
    private $ILogger = null;

    private function setLogger($Ilogger)
    {
        $this->Ilogger = $Ilogger;
    }
    /**
     * Termina la conexion actual (Si existe) y establece una nueva al mismo servidor con mismo usuario pero a diferente BD (o esquema segun gestor).
     * Es decir, solo cambia la bd a la cual estamos conectados. A diferencias de los demas metodos, este se conecta desde ya(Eager loading) y devuelve la conexion para verficar que se conecto
     *
     *
     *
     * @param string $databaseName
     * @return PDO
     */
    public function ReconnectToDatabase(string $databaseName, bool $throwExceptionIfFails = false): ?\PDO
    {
        $this->close(); //null a la conexion actual

        if (empty($this->databaseName)) {
            throw new \Lindan\Data\PDODException('Nombre de la bd vacio');
        }
        if (empty($this->userName)) {
            throw new \Lindan\Data\PDODException('Nombre de usuario vacio');
        }

        $this->setDataBaseName($databaseName); //establecemos nombre
        try {
            return $this->connect(true); //
        } catch (\PDOException $e) {
            if ($throwExceptionIfFails) {
                throw $e;
            }
        }
        return null;

    }
    const DATABASE_TYPE_POSTGRES='pgsql';
    const DATABASE_TYPE_MYSQL='mysql';
    public function setDataSource(array $dataSource)
    {
        //si es un array extraemos los datos:
        //if (is_array($dataSource)) {
        $this->databaseName = $dataSource[self::database_name];
        $this->host = $dataSource[self::database_host];
        $this->userName = $dataSource[self::database_user];
        $this->userPassword = $dataSource[self::database_password];

        if(isset($dataSource[self::database_type])){
            if(!\in_array($dataSource[self::database_type],[self::DATABASE_TYPE_POSTGRES,self::DATABASE_TYPE_MYSQL])){
                throw Exception('Tipo de base de datos no soportada');
            }
            $this->databaseType=$dataSource[self::database_type];
        }

        $this->port=$dataSource[self::database_port]??5432;
        //$this->port=$dataSource[database_port];
        //si es una instancia de iDataSource extreemos los datos
        //}
        //else if (is_object($DataSource) && ( $dataSource instanceof IDataSource)) {

        //}
    }

    public function __construct(array $dataSource = [], bool $production = true)
    {
        $this->production = $production;
        if (!empty($dataSource)) {
            $this->setDataSource($dataSource);
        }
    }
    /**
     * Crea la conexion \PDO a la base de datos, si ya existe una conexion se reutiliza.
     *
     *
     * @param boolean $throwExceptionIfFails por defecto false. Controla si lanza la exception.
     * @return \PDO
     */
    public function connect(bool $throwExceptionIfFails = false): ?\PDO
    {
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //lanzar excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //que sea asocitatovp
            PDO::ATTR_EMULATE_PREPARES => false,

        ];
        if ($this->connection !== null) //retornamos si ya existe la conexion
        {
            return $this->connection;
        }

        try {
            //$dsn="pgsql:dbname=$this->databaseName ;host= $this->host ;port=$this->port; charset=$this->charset ;";

          //  $dsn = "pgsql:dbname=$this->databaseName ;host= $this->host ;port=$this->port;";
            $dsn='';
            switch ($this->databaseType)
            {
                case self::DATABASE_TYPE_POSTGRES:
                $dsn = "pgsql:dbname=$this->databaseName ;host= $this->host ;port=$this->port;"; //postgres no toma los espacios(ni en windowns , ni linux, Mysql si los toma)
                break;
                case self::DATABASE_TYPE_MYSQL:
                $dsn = "mysql:dbname=$this->databaseName;host=$this->host;port=$this->port;"; // en mysql si se consideran los espacios , por lo que mejor los removi
                break;
                default:
                    throw new \Exception("Database type not defined for this class. see @author Daniel  Hdz", 1);
                    
            }



            if (php_sapi_name() == 'cli') {
                echo $dsn;
                //echo $this->userPassword;
            }
            if (\is_bool($this->userPassword)) {
                //echo "contraseña ".$this->userPassword;
            }            
            $this->connection = new \PDO(
                $dsn,
                $this->userName,
                $this->userPassword,
                $opt);
            if(self::DATABASE_TYPE_POSTGRES==$this->databaseType){    
                $this->connection->query("SET CLIENT_ENCODING TO 'UTF8'"); //https://www.postgresql.org/docs/9.3/static/multibyte.html
            }
        } catch (\PDOException $e) {
            $this->logError("Error en construccion de PDO pass: $this->userPassword");
            $this->logError($e);

            $this->setError($e->getMessage());
            // if ($throwExceptionIfFails) {
            throw $e;
            // }
        }
        return $this->connection;
    }
    /**
     * Inicia la trasaccion, ademas si la conexion no ha sido abierta, la abrira.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->connect();
        return $this->connection->beginTransaction();
    }
    public function rollBack()
    {
        return $this->connection->rollBack();
    }
    public function commit()
    {
        return $this->connection->commit();
    }
    /**
     * Retorna el ultimo id insertado de la conexion
     *
     * @return void
     */
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
    /**
     * Inserta un registro y devuelve el id.
     *
     * Nota para que esto funcione correctamente no tiene que existir otras inserciones esta  misma conexion,
     *  ya que solo se asegura de devolver el ultimo idInsertado de la conexion.
     * Es decir, no se recomienda para operaciones paralelas que compartan esta instancia de clase
     *
     * Nota:internamenete usa commandParams() y lastInsertId()
     * Puede arrojar el error:Object not in prerequisite state,sino usar RETURNING id
     * @param string $sql con formato insert into de insercion.
     * @param array $params
     * @param array $returnLastId por default es true e indica que devolvera el id generado, indicar false cuando el id no es generado sino es asignado de forma manual (un string )
     * @return int
     */
    public function InsertParams(string $sql, array $params = [], bool $returnLastId = true)
    {
        $this->commandParams($sql, $params);
        #lastval no está definido en esta sesión, segun puede ser porque no es la misma conexion donde se inserto.si ocurre setea  returnLastId a false
        return $returnLastId === true ? $this->lastInsertId() : 0; //note , para que esto funcione la conexion no debe cerrarse
    }
    /**
     * Checa si el sql es valido y si hay conexion
     *
     * @param string $sql
     * @return void
     */
    public function CheckCommandAndConnection(string $sql)
    {
        if (trim($sql) == '') {
            throw new \Exception("PdoPgConnection->command:Falta indicar el comando");
        }
        if (!$this->connection) {
            throw new \Exception("PdoPgConnection->falta conexion bd");
        }
    }
    /**
     * Ejecuta un comando de sql
     * @param string $sql
     * @param array $params
     * @return mixed true en caso de exito, false en caso de fallo, PDOobjet en caso de error.
     * @throws \Exception
     */
    public function commandParams(string $sql, array $params = array())
    {
        $this->connect();
        $this->CheckCommandAndConnection($sql);
        $res = null;
        try {
            $stmt = $this->connection->prepare($sql);
            // preparedStatament->excute() Devuelve TRUE en caso de éxito o FALSE en caso de error.
            $res = $stmt->execute($params);

        } catch (\PDOException $e) {
            $this->setError($e->getMessage() . ':' . $sql . \print_r($params, true));
            throw new \PDOException("Error al ejecutar sentencia $sql Parametros:".print_r($params,true).
            'Exception en: '.$e->getMessage().'DB:'.$this->databaseName, 0, $e);

        } catch (\Exception $e) {
            $this->setError($e->getMessage() . ':' . $sql . \print_r($params, true));
            throw $e;
        }
        return $res;
    }
    /**
     * Esta function recibe el sql y los parametros a ejecutar,
     * adicional permite incluir en el tercer elemento el tipo de dato
     *
     * @param string $sql
     * @param array $params
     * @return void
     */
    public function commandParamsBind(string $sql, array $params = [])
    {
        $this->connect();
        $this->CheckCommandAndConnection($sql);
        $res = null;

        try {

            $stmt = $this->connection->prepare($sql);
            foreach ($params as $param) {

                if (count($param) < 3) {
                    //si son menos de 3 parametros entonces solo se indico nombre y valor, por eso agregamos que por defecto sea string
                    //throw new \Exception('Se esperan 3 parametros para bindParam');
                    \array_push($param, \PDO::PARAM_STR);
                }
                //position 0 is the key,1 is the value and 2 is the datatype
                $stmt->bindParam($param[0], $param[1], $param[2]);
            }
            $res = $stmt->execute();

        } catch (\PDOException $e) {
            $this->setError($e->getMessage() . ':' . $sql);
            throw new \PDOException("Error al ejecutar sentencia $sql  .Parametros:".print_r($params,true).$e->getMessage(), 0, $e);

        } catch (\Exception $e) {
            $this->setError($e->getMessage() . ':' . $sql);
            throw $e;
        }
        return $res;
    }
    /**
     *  Rescata el primer elemento de una consulta, el usuario es respondesable delq ue la consulta devuelva un solo resultado como limit 1, si no hay resultados devuelve null, tambien es posible recibir un callback para mapear el unico resultado a una entidad de clase por ejemplo.
     * Si no hay resultados retorna null, si hay al menos un resultado devolvera lo que hay del callback , si no se recibe el callback, devolvera un array
     *
     * @param string $sql
     * @param array $params
     * @param callable $callback
     * @return mixed Si no hay resultados retorna null, si hay al menos un resultado devolvera lo que hay del callback , si no se recibe el callback, devolvera un array
     */
    public function querySingleParams(string $sql, array $params = array(), callable $callback = null,callable $func2=null)
    {
        // echo "entraa";
        $res = $this->queryParams($sql, $params);
        // die("querysim");
        if ($res && is_array($res) && sizeof($res) > 0) {
            if (null == $callback) {
                return $res[0];
            } else {

                if(null==$func2){ //si es null solo recibimos el primer callback
                    return $callback($res[0]);
                }else{//entonces recibimos ambos
                    return $func2($callback($res[0]),$res[0]); //esto permite que reutice una funcion mapper
                    // y con func2 especialize mas el mapper, ejemplo: supogamos que hay varios querys que consultan la misma tabla, pero es posible que compartan varios campos entre si.
                    //entonces extrapolaria todos esos campos comunes en el mapper 1 =func , y luego a cada variacion lo especializaria con $func2, por eso func2 debe recibir tanto el objeto retornado por $func como el row
                }
                //return $callback($res[0]);
            }
        } else {
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
     * @param integer $fectch es el metodo de PDO para el resultado de la base de datos, por defecto es asociativo columna_nombre-valor
     * @return mixed
     */
    public function queryParams(string $sql, array $params = array(), int $fectch = PDO::FETCH_ASSOC)
    {
        // echo "queryParams";
        $this->connect();
        //echo "aqui--";
        $this->checkSqlAndConnection($sql);
        if ($this->getError()) {
            return null;
        }
        //echo "\n aquiquerypara";
        $res = null;
        try {
            $stmt = $res = $this->connection->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetchAll($fectch);
           // print_r($res);
            // echo "\try";
        } catch (\PDOException $pdoE) {
            $this->setError($pdoE->getMessage() . "   Error al ejecutar la sentencia :" . $sql);
            $new = new \PDOException("Error al ejecutar la sentencia $sql . Error:".$pdoE->getMessage(), 1, $pdoE);
            throw $new; //lanzamos la excepcion, es responsabilidad del usuario catcharla en algun punto
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            //throw $e;
        }
        return $this->tratarResultQuery($res);
    }
    /**
     * Funcion que ejecuta una consulta y la mapea en caso de obtener el array de la consulta,
     * el callback recibe cada row de la consulta y debera mapearlo al objeto que desee,
     * cada objeto sera agregado a un arrayMapped que sera la respuesta de este metodo.
     *
     * @param string $sql
     * @param array $params
     * @param callable $func
     * @return array
     */
    public function query(string $sql, array $params = [],callable $func,callable $func2=null): array
    {
        $this->connect();
        $this->checkSqlAndConnection($sql);
        if ($this->getError()) {
            return [];
        }
        //$fectch = PDO::FETCH_BOTH;// (default): returns an array indexed by both column name and 0-indexed column number as returned in your result set 
        $fectch = PDO::FETCH_ASSOC;//returns an array indexed by column name as returned in your result set 
        $res = null;
        try {
            $stmt = $res = $this->connection->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetchAll($fectch);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
        $arrMapped = [];
        if ($res && \is_array($res)) {
            foreach ($res as $item) {
                if(null==$func2){
                    $arrMapped[] = $func($item);
                }else{
                    $arrMapped[]=$func2($func($item),$item); //esto permite que reutice una funcion mapper
                    // y con func2 especialize mas el mapper, ejemplo: supogamos que hay varios querys que consultan la misma tabla, pero es posible que compartan varios campos entre si.
                    //entonces extrapolaria todos esos campos comunes en el mapper 1 =func , y luego a cada variacion lo especializaria con $func2, por eso func2 debe recibir tanto el objeto retornado por $func como el row
                }
            }
            return $arrMapped;
        }
        if ($res === false) {
            throw new \Exception("Error en consulta: $sql");
        }
        return [];
    }
    /**
     * metodo interno que se encarga de controlar el resultado de una consulta
     * creado ya que query y queryParams tienen y deberan tener la misma logica de resulado
     * @param type $res
     * @return type+
     */
    private function tratarResultQuery($res)
    {
        //Logger::info('tratarResultQuery:');
        if ($this->auto) {
            // $this->close();
        }
        if ($res) {
            if (is_array($res) && sizeof($res) == 0) {
                return null;
            }
        } else {
            return $res;
        }
        //Logger::info( 'return res:'.$res);
        return $res;
    }
    private function checkSqlAndConnection(string $sql)
    {
        if ($sql == '') {
            $this->setError('AcessoBD->realizarConsulta:Falta indicar la consulta');

        }
        if (!$this->connection) {
            $this->setError('AcessoBD->realizarConsulta:Falta conexion a la BD');

        }
        if (!$this->connection instanceof \PDO) {
            $this->setError('No es un objeto pdo');
        }
        $this->setError(null);
    }

    public function querySimple(string $sql,int $fetchMode=PDO::FETCH_ASSOC)
    {
       // Logger::info('querySimple');
        $this->connect();
        //echo "aqui--";
        $this->checkSqlAndConnection($sql);
        if ($this->getError()) {
            return null;
        }
        //echo "\n aquiquerypara";
        $res = null;
        try {
            $res = $this->connection->query($sql, $fetchMode);
         //   Logger::info('ejecutada:' . $sql);
        } catch (\Exception $e) {
         //   Logger::error('$e->getMessage():' . $sql);
            $this->setError($e->getMessage());
        }
        if ($res instanceof \PDOStatement) {
            return $res->fetchAll();
        }
        return $this->tratarResultQuery($res);
    }
    /**
     * Funcion Wrrapper de exec
     *
     * @param string $sql
     * @return void
     */
    public function exec(string $sql)
    {
        $this->connect();
        //echo "aqui--";
        $this->checkSqlAndConnection($sql);
        if ($this->getError()) {
            return null;
        }
        try {
            $this->connection->exec($sql);
        } catch (\PDOException $e) {

            if ($sql == "") {
                Logger::error("sql vacio");
            }
            if (null == $sql) {
                Logger::error("sql es null");
            }
            //colocar un logger aqui!!!
            throw $e;
        }
    }
    /**
     * Cierra la conexion PDO, establece la conexion a null,por lo cual todas las subsecuentes operaciones reabrirarn la conexion con los datos de conexion con las propidades.
     *
     * Esto es util por ejemplo si deseamos conectarnos a otra Base de datos usando el mismo usuario.
     * @return void
     */
    public function close()
    {
        //de acuerdo a la documentacion para la coneccion se mantiene activa por el tiempo de vuda del objeto pdo
        $this->connection = null;
    }
    /**
     * Retorna true si existe al menos un row de la consulta dada,en caso contrario devulve false.
     *
     * Notar que es responsabilidad del usuario establecer un limit para cortar la busqueda a fin de optimizar la busqueda
     *
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function existAtLeastOne(string $sql, array $params = []): bool
    {
        $this->connect();
        $this->checkSqlAndConnection($sql);
        if ($this->getError()) {
            return [];
        }
        $fectch = PDO::FETCH_BOTH;
        $res = null;
        try {
            $stmt = $res = $this->connection->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetchAll($fectch);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
            
        }
        $arrMapped = [];
        if ($res && \is_array($res) && \count($res) > 0) {
            return true;
        }
        if ($res === false) {
            throw new \Exception("Error en consulta: $sql");
        }
        return false;
    }

    ####################################################################################################
    private $sql;
    private $bindParams = [];

    /**
     * Guarda la sentencia sql hasta invocar al metodo execute
     *
     * @param string $sql
     * @return PdoPgConnection
     */
    public function prepare(string $sql): PdoPgConnection
    {
        $this->sql = $sql;
        return $this;
    }
    /**
     * Apila el parametro en un array que luego sera usado en el metodo execute
     *
     * @param string $parameter
     * @param string $variable
     * @param integer $dataType
     * @return PdoPgConnection
     */
    public function bindParam(string $parameter, string $variable, int $dataType = \PDO::PARAM_STR): PdoPgConnection
    {
        $this->bindParams[] = [$parameter, $variable, $dataType];
        return $this;
    }
    /**
     * Ejecuta la sentencia preparada
     *
     * @return mixed
     */
    public function execute()
    {
        $sql = $this->sql;
        $bindParams = $this->bindParams; //los guardamos en una variable para resetear
        $this->sql = '';
        $this->bindParams = [];
        print_r($bindParams);
        return $this->commandParamsBind($sql, $bindParams);
    }
    #####################################################################################################

    /**
     * @author Daniel <danie.hernandez.fco@gmail.com>
     * Esta funcion esta inspirada en la idea que de al usar prepared staments, al utilizar comodines o alias para los parametros y en general su nombre no importa mas que el orden, es mejor reutlizar el nombre de los atirbutos.
     * Ps = preparet stament
     *NOta: internamente usa InsertParams() que asu vez usa CommandParams()
     * @param string $tableName
     * @param array $params, donde el key de cada valor es el nombre del atirbuto de la tabla.
     * @param array $returnLastId por default es true e indica que devolvera el id generado, indicar false cuando el id no es generado sino es asignado de forma manual. Se debe considerar entonces que unicamente aplica para campos que son numericos y autogenerados por el gestor de bd.
     * @return null|mixed
     * @throws PDODException
     * @throws PDOException
     */
    public function InsertPS(string $tableName, array $params, bool $returnLastId = true,string $returning="")
    {

        $keys = \implode(',', \array_keys($params));
        //$keysParams=\implode(',:', \array_keys($params)); //no aplica
        //  $values=\implode(',',\array_values($params));
        $newArray = self::FormatArrayPlaceHoldersKeysForCommandParams($params);
        $keysParams = implode(',', \array_keys($newArray));
        if (count($params) !== count($newArray)) {
            throw new \Lindan\Data\PDOException('Error en numero de elementos' . print_r($keys, true) . print_r($newArray, true));
        }
        #solo si returnLastId es true, es como se concatenara lo que tenga returning
        if($returnLastId){
            $sql = 'INSERT INTO ' . $tableName . '  ( ' . $keys . ') VALUES (' . $keysParams . ') '.$returning.';'; 
        }else{
           $sql = 'INSERT INTO ' . $tableName . '  ( ' . $keys . ') VALUES (' . $keysParams . ');';
        }
        return $this->InsertParams($sql, $newArray, $returnLastId);

    }
    /**
     * Prepara un arreglo de la forma ':nombrecolumna'=>valor,
     *  es decir, agrega un : al principio del key de cada elemento para ser usado como placeholder
     *
     * @param array $params
     * @return array
     */
    private static function FormatArrayPlaceHoldersKeysForCommandParams(array $params, bool $operators=false)
    {
        $newArray = [];
        foreach ($params as $key => $value) {
            if($operators){//si tiene operator entonces debo eliminarlos antes de formar el arrelgo de parametros.
                $key=preg_replace("/(\s*\[(JSON|\+|\-|\*|\/)\]$)/i", '',$key);
            }            
            $newArray[':' . $key] = $value; //le agrego el prefijo ':'
            
            
        }
        return $newArray;
    }
    /**
     * Funcion basada en el concepto de InsertPS que usa prepared statements .
     *
     * Recibe un arreglo con los paramtros a modificar basado en los valores recibidos,ojo , no soporta sumas, restas o multiplicaciones con los valores ya existentes en el registro,por lo cual esta funcion es aun limitada.
     * el array where son las condiciones a cumplir, esta version solo permite el operador de igualdad en el where, no mayor o menor, o between.
     *
     * @param string $tableName
     * @param array $params
     * @param array $where , la condicion a cumplir, en esta version solo permite condiciones con operador de igualdad y pueden ser mutlples valores, si el array esta vacio entonces no habra clausula where, por tanto es responsabildiad del programador usar correctamente este metodo
     * @return void
     */
    public function UpdatePS(string $tableName, array $params, array $where)
    {

        $updateSetParams = self::FormatArrayPlaceHoldersKeysForCommandParams($params);
        $whereSetParams = self::FormatArrayPlaceHoldersKeysForCommandParams($where);
        //le agregamos una , como separador, usamos implode porque no agrega el separador al principio ni al final, despues de todo agrega un separador y no hay nada que separe al inicio o al final.
        //esta function inserta las columnas con sus comodines columna_nombre=:columna_nombre
        $asignarPlaceHolderAColumnas = function ($arrayAsociativo) {
            $set = [];
            foreach ($arrayAsociativo as $key => $value) {
                
                $set[] = $key . '=:' . $key; //columna_c=:columna_c
            }
            return $set; //retornamos un array basado en indice
        };
        $updateSet = $asignarPlaceHolderAColumnas($params);
        $updateSet = implode(',', ($updateSet));

        $whereSet = $asignarPlaceHolderAColumnas($where);
        //\var_dump($whereSet);
        //es posible que el array where sea vacio, entonces no hay clausula where
        $whereSet = count($where) === 0 ? '' : ' where ' . implode(' AND ', $whereSet);

        $sql = 'UPDATE ' . $tableName . ' SET ' . $updateSet . $whereSet . ';';

        //unimos los parametros
        $totalParams = array_merge($updateSetParams, $whereSetParams);
        //echo $sql . "\n";
        $this->commandParams($sql, $totalParams);

    }
    protected function typeMap($value, $type)
    {
        $map = [
            'NULL' => PDO::PARAM_NULL,
            'integer' => PDO::PARAM_INT,
            'double' => PDO::PARAM_STR,
            'boolean' => PDO::PARAM_BOOL,
            'string' => PDO::PARAM_STR,
            'object' => PDO::PARAM_STR,
            'resource' => PDO::PARAM_LOB,
        ];

        if ($type === 'boolean') {
            $value = ($value ? '1' : '0');
        } elseif ($type === 'NULL') {
            $value = null;
        }

        return [$value, $map[$type]];
    }
    /**
     * Recibe un array con la configuracion, el array tiene la forma de
     *  'nombre_de_conexion'=>[
     *      'database_host'=>localhost,
     *      'database_name'=>'databaseName',
     *      'database_user'=>'database_user',
     *      'database_password'=>'1234565',
     *      'database_driver'=>'' (para clase generica)
     *  ]
     * @param array $arr [description]
     */
    public function AddConnection(array $arrConnection)
    {
        if (1 !== count($arrConnection)) {
            throw new PDODException('Solo debe existir un item el primer nivel(nombre de la conexion');
        }
        if (!self::has_string_keys($arrConnection)) {
            throw new PDODException('El arreglo no parece ser un array asocitivo valido');
        }

        $connectionName = array_keys($arrConnection)[0];
        $error = self::isValidDataSource($arrConnection[$connectionName]);
        if (!empty($error)) {
            throw new PDODException($error);
        }
        //metemos o si existe sobreescribe
        $arrConnection[$connectionName] = $arrConnection[$connectionName];
    }
    /**
     * Arreglo de dataSources
     *
     * @var array
     */
    private $arrConnections = [];

    private static function has_string_keys(array $array): bool
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
    private static function isValidDataSource(array $arrDataSource): string
    {
        /*
        $error=isset($arrDataSource[self::database_name])?'':'Falta indicar nombre de base da datos';
        $error=isset($arrConnection[self::database_host])?$error:'Falta indicar host';
        $error=isset($arrConnection[self::database_user])?$error:'falta indicar usuario';
        $error=isset($arrConnection[self::database_password])?$error:'falta indicar pass';

         */

        //usando el operador ternario puedo optimizar y sintetizar mejor la validacion ya que va por orden, ejemplo si no esta indicada la database_name se le asigna el error y termina , pero si esta ok, entonces pasamos a la siguiente, y asi sucesivamente:
        $error = !isset($arrDataSource[self::database_name]) ? 'Falta indicar nombre de base da datos' :
        (!isset($arrConnection[self::database_host]) ? 'Falta indicar host' :
            (!isset($arrConnection[self::database_user]) ? 'falta indicar usuario' :
                (!isset($arrConnection[self::database_password]) ? 'falta indicar pass' : '' //todo ok
                )));

        return $error;
    }
     /**
     * Verifica si existe (si tiene permisos) la base de datos con el nombre
     */
    public function DatabaseExistsInMyConnection(string $databaseName):bool{
        echo "$databaseName";
        return $this->existAtLeastOne('SELECT datname from pg_database WHERE datname=:datname;',
            [':datname'=>$databaseName]);
    }
    /**
     * Wrapper de execute . a diferencia de query o queryParams, este no devuleve ningun resultado
     *
     * @param string $sql
     * @param array $params
     * @return void
     */
    public function executeParams(string $sql, array $params = []): void
    {
        $this->connect();
        $this->checkSqlAndConnection($sql);
        if ($this->getError()) {
            return ;
        }
        $res = null;
        try {
            $stmt = $res = $this->connection->prepare($sql);
            $resExe= $stmt->execute($params);
            
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            throw $e;
        }
        
        if ($res === false) {
            throw new \Exception("Error en consulta: $sql");
        }
        return ;
    }



    /**
     * Funcion basada en el concepto de InsertPS que usa prepared statements .
     *
     * Recibe un arreglo con los paramtros a modificar basado en los valores recibidos,ojo SI SOPORTA, restas o multiplicaciones con los valores ya existentes en el registro.
     * Ejemplo :
     *  ['total[+]'=>13] es igual que total=total+13
     *  ['total[-]'=>13]  es igual que total=total-13
     *  ['total[*]'=>13]  es igual que total=total*13
     *  ['total[*]'=>13]  es igual que total=total/13
     * 
     *  Sintaxis es " columna=columna Operator :columna"
     *  Esta function es un mejora de UpdatePS, sin embargo ocupa ligeramente mas recursos ya que debe extraer el operador.
     * el array where son las condiciones a cumplir, esta version solo permite el operador de igualdad en el where, no mayor o menor, o between.
     *
     * @param string $tableName
     * @param array $params
     * @param array $where , la condicion a cumplir, en esta version solo permite condiciones con operador de igualdad y pueden ser mutlples valores, si el array esta vacio entonces no habra clausula where, por tanto es responsabildiad del programador usar correctamente este metodo
     * @return void
     */
    public function UpdatePSWithOperators(string $tableName, array $params, array $where)
    {

        $updateSetParams = self::FormatArrayPlaceHoldersKeysForCommandParams($params,true);
        $whereSetParams = self::FormatArrayPlaceHoldersKeysForCommandParams($where,true);
        //le agregamos una , como separador, usamos implode porque no agrega el separador al principio ni al final, despues de todo agrega un separador y no hay nada que separe al inicio o al final.
        //esta function inserta las columnas con sus comodines columna_nombre=:columna_nombre
        $asignarPlaceHolderAColumnas = function ($arrayAsociativo,bool $IsArrForset) {
            $set = [];
            foreach ($arrayAsociativo as $key => $value) {
                //$IsArrForset indica si es para el SET o un where. si es true entonces es para set y se debe buscar y limpiar de los operadores.
                $keyCleaned =$IsArrForset? preg_replace("/(\s*\[(JSON|\+|\-|\*|\/)\]$)/i", '',$key):$key;
                $match=array();
                preg_match('/(?<column>[a-zA-Z0-9_]+)(\[(?<operator>\+|\-|\*|\/)\])?/i', $key, $match);
                if (isset($match[ 'operator' ]))//checamos si tiene operador
                {
                    if (is_numeric($value)) //si el valor es numerico
                    {
                                //columna = columna + valor; osea columna=columna + :columna
                        $set[] = $keyCleaned . ' = ' . $keyCleaned . ' ' . $match[ 'operator' ] . ' :' . $keyCleaned;
                    }
                }else {
                //aqui se hace el columna =:variable
                $set[] = $key . '=:' . $key; //columna_c=:columna_c    
                }

                
            }
            return $set; //retornamos un array basado en indice
        };
        $updateSet = $asignarPlaceHolderAColumnas($params,true);
        $updateSet = implode(',', ($updateSet));

        $whereSet = $asignarPlaceHolderAColumnas($where,false);
        //\var_dump($whereSet);
        //es posible que el array where sea vacio, entonces no hay clausula where
        $whereSet = count($where) === 0 ? '' : ' where ' . implode(' AND ', $whereSet);

        $sql = 'UPDATE ' . $tableName . ' SET ' . $updateSet . $whereSet . ';';

        //unimos los parametros
        $totalParams = array_merge($updateSetParams, $whereSetParams);
       // echo $sql . "\n";
       // print_r($totalParams);
        $this->commandParams($sql, $totalParams);

    }
    protected function isRaw($object)
	{
		return $object instanceof Raw;
    }
    public static function Raw($value){
        $raw=new Raw();
        $raw->value=$value;
        return $raw;
    }
}
class Raw{
    public $map;
	public $value;
}
/**
 * Clase de excepciones para esta Bliclioteca
 */
class PDODException extends \Exception
{

}
class LoggerPDO
{

    public static function renderExceptionOrError($exception)
    {
        if (!$exception instanceof \Exception && !$exception instanceof \Error) {
            throw new \RuntimeException("Unexpected type. Expected Exception or Error.");
        }

        $html = sprintf(' Type:%s ', get_class($exception));

        if (($code = $exception->getCode())) {
            $html .= sprintf('Code: %s', $code);
        }

        if (($message = $exception->getMessage())) {
            $html .= sprintf('Message: %s', ($message));
        }

        if (($file = $exception->getFile())) {
            $html .= sprintf('File: %s', $file);
        }

        if (($line = $exception->getLine())) {
            $html .= sprintf('Line: %s', $line);
        }

        if (($trace = $exception->getTraceAsString())) {
            $html .= 'Trace';
            $html .= sprintf('%s', ($trace));
        }

        return $html;
    }
}
