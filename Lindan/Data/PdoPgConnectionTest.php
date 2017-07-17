<?php

namespace Lindan\Data;

/**
 * clase de pruba PdoPgConnectionTest para la clase pdoPgConnection.php
 *
 * @author daniel
 */
include_once 'PdoPgConnection.php';
class PdoPgConnectionTest {
    public function testQueryParamsSingle(){
        $pdo= new PdoPgConnection();
        $sql="select * from usuario limit 1";
        $res=$pdo->querySingleParams($sql);
        var_dump($res);        
    }
    public function testQueryParamsAll(){
        $pdo= new PdoPgConnection();
        $sql="select * from usuario ";
        $res=$pdo->queryParams($sql);
        var_dump($res);        
    }
    public function testQueryParams(){
        $pdo= new PdoPgConnection();
        $sql="select * from usuario where id=:id";
        $res=$pdo->queryParams($sql, array("id"=>1));
        var_dump($res);
        
    }
    public function testQueuryParamsNoAsociativo() {
        $pdo= new PdoPgConnection();
        $sql="select * from usuario where id=?";
        $res=$pdo->queryParams($sql, array(1));
        var_dump($res);
    }
    public function testCommandParams() {
        $pdo= new PdoPgConnection();
        $sql="insert into usuario(id,nombre,edad,pass) values(:id,:nombre,:edad, :pass)";
        $res=$pdo->commandParams($sql, array("id"=>102,"nombre"=>"ñoño de loépz ' ka","edad"=>23,"pass"=>"daniel"));
       if($res===true){
           echo "ok;";
       }
        var_dump($res);
    }
}
$pdo= new PdoPgConnectionTest();
//$pdo->testQueryParams();
//$pdo->testCommandParams();
//$pdo->testQueuryParamsNoAsociativo();
//$pdo->testQueryParamsAll();
$pdo->testQueryParamsSingle();
