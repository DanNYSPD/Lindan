<?php 
include_once("ILogger.php");
class LoggerMysqliEcho implements ILogger{


	public function imprimirError($mysqli){
		echo "Error: Fallo al conectarse a MySQL debido a: \n";
	    echo "Errno: " . $mysqli->connect_errno . "\n";
	    echo "Error: " . $mysqli->connect_error . "\n";
	}
	public  function imprimirErrorSQL($mysqli,$sql)
	{
				echo "</br>Lo sentimos, este sitio web está experimentando problemas.";

			    // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
			    // cómo obtener información del error
			    echo "Error: La ejecución de la consulta falló debido a: \n";
			    echo "Query: " . $sql . "\n";
			    echo "Errno: " . $mysqli->errno . "\n";
			    echo "Error: " . $mysqli->error . "\n";
	}
}

 ?>