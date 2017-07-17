<?php 

namespace Lindan\Data;

interface IDataBaseConnection{
	
    public function __construct($fromFile = false);
    public function connect();

    //retorna true si todo fue ok
    public function command($sql);


   
   

  

    public function query($sql, $mode = MysqlConnection::MODE_ASSOC);
    /**
     * @param  [type]
     * @param  [type]
     * @return [type] devuelve UNA SOLA fila del primer resultado (aplica intermente un array[0])
     */
    public function querySingle($sql);

  //  public function prepare($sql);

    public function close();
   
}



?>