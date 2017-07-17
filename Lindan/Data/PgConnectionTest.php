<?php

namespace Lindan\Data;

require_once 'PgConnection.php';

class PgConnectionTest {

    //put your code here

    public function testQuery() {
        $pgConnection = new PgConnection();
        $res = $pgConnection->query("select * from usuario");
        var_dump($res);
        /*    foreach ($res as $key => $value) {
          var_dump($value);
          }
         * */
    }

    /*
     * probarmos cuando no hay coincidencia:
     */

    public function testQueryEmpty() {
        $pgConnection = new PgConnection();
        $res = $pgConnection->query("select * from usuario where pass='7'");
        if ($res) {
            echo "No es emoty";
        } else {
            echo "es emtpy";
        }
    }

    /*
     * probarmos cuando consultamos una tabla que no existe:
     */

    public function testQueryTablaNoExiste() {
        $pgConnection = new PgConnection();
        $res = $pgConnection->query("select * from lallala");
        var_dump($res);
    }

    public function testQuerySingle() {
        $pgConnection = new PgConnection();
        $res = $pgConnection->querySingle("select * from usuario limit 1");
        var_dump($res);
    }
    public function testInsert() {
        $sql="insert into usuario (id,nombre,edad,pass) values (8,'se',21,ada);";
         $pgConnection = new PgConnection();
        $res = $pgConnection->command($sql);
        var_dump($res);
    }
    public function testQueryParamsOk() {
      $pgConnection = new PgConnection();
        $res = $pgConnection->queryParams("select * from usuario where id= $1",array("2"));
        var_dump($res);   
    }
    public function testCommandParams() {
         $sql="insert into usuario (id,nombre,edad,pass) values ($1,$2,$3,$4);";
         $pgConnection = new PgConnection();
        $res = $pgConnection->commandParams($sql,array(7,"hernández llañ",50,"'lope'z lála"));
        //$res = $pgConnection->commandParams($sql,array(7,"hernández llañ",50,"'lope'z lála"));
        var_dump($res);
    }
}

$pgConnectionTest = new PgConnectionTest();


//$pgConnectionTest->testQuery();
//$pgConnectionTest->testQueryEmpty();

//$pgConnectionTest->testQueryTablaNoExiste();
//$pgConnectionTest->testQuerySingle();
//$pgConnectionTest->testInsert();
$pgConnectionTest->testQueryParamsOk();

//$pgConnectionTest->testCommandParams();
//$string ="lallalallalala jesu´s";
//echo $string;

