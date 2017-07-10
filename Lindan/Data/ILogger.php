<?php 

interface ILogger{

	public function ImprimirError($mysqli);
	public function imprimirErrorSQL($mysqli,$sql);
}
 ?>