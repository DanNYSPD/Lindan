<?php 
/**
 * @author [Daniel Jesus Hernandez Fco] <[<danie.hernandez.fco@gmail.com>]>
 */
interface IDataSource{
 public function getDatabaseName();
 public function getDatabasePort();
 public function getDatabaseUser();
 public function getDataBasePass();
 public function getHostName();
}
?>