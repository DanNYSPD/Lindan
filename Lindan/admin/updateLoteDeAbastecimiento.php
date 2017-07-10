<?php
session_start();
include_once('Services/response/JsonResponse.php');
include '../conexion2.php'; 

if(isset($_SESSION['user'])){
    //echo "";
}else{
    //echo '<script>window.location="Administrador.php";</script>';
    errorResponse("no estas autorizado,inicia sesion",401);
}
 	
	require '../conexion2.php';
	
	$Id_manzana = $_POST['Id_manzana']; //no edistable

	$Num_lote = $_POST['Num_lote'];
	$Num_hab = $_POST['Num_hab'];
	$Colonia = $_POST['Colonia'];
	
	
	
	$sql = "UPDATE lotesxhabitantes SET Id_manzana='$Id_manzana', Num_lote='$Num_lote', Num_habitantes='$Num_hab', Colonia='$Colonia'  WHERE Id_manzana= '$Id_manzana'";
	
	$resultado = mysqli_query($mysqli, $sql);
	



				 if($resultado) 
					{ 
				$JsonResponse= new JsonResponse(200); 
				$JsonResponse->response(array("msj"=>"Se actualizo correctamente"));
				    } 
				    else 
				    	{ 
				    		errorResponse("No se pudo realizar el cambio en el $Id_manzana",404);
				}


function errorResponse($msj,$code=404)
{
	$JsonResponse= new JsonResponse($code); 
	$JsonResponse->response(array("msj"=>$msj));
	die();

}