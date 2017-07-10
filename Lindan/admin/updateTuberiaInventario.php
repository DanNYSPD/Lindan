<?php
session_start();
include_once('Services/response/JsonResponse.php');
include '../conexion2.php'; 

if(isset($_SESSION['user'])){
    //echo "";
}else{
  	
    errorResponse("no estas autorizado,inicia sesion",401);
}

	
	require '../conexion2.php';
	
	$Id_tuberia=$_POST['Id_tuberia'];
	$Diametro= $_POST['Diametro'];
	$Material = $_POST['Material'];
	$Metros = $_POST['Metros'];
	
	
	
	$sql = "UPDATE tuberia SET   Diametro='$Diametro', Material='$Material', Metros='$Metros'  WHERE Id_tuberia= '$Id_tuberia'";
	
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