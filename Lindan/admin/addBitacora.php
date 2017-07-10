<?php
session_start();

include_once 'Services/BitacoraService.php';
include_once 'Services/response/JsonResponse.php';


if(isset($_SESSION['user'])){
    
}else{
    errorResponse("no estas autorizado,inicia sesion",401);
}
	const key_fecha='Fecha';
	const key_trabajo='Trabajo';
	const key_monto='Monto';
	const key_pozoId='pozoid';
	if(empty($_POST[key_fecha])||empty($_POST[key_trabajo])||empty($_POST[key_monto])||empty($_POST[key_pozoId])){

		errorResponse("error, faltan atributos",400);
	}else{
		//echo "niguno es empty(var)";
	}
	$Fecha = $_POST['Fecha'];
	$Trabajo = $_POST['Trabajo'];
	$Monto = $_POST['Monto'];
	$PozoId=$_POST["pozoid"];
	
	$BitacoraService = new BitacoraService();
	$resultado=$BitacoraService->addBitacora($Fecha,$Trabajo,$Monto,$PozoId);
	 if($resultado) 
	{ 
		$JsonResponse= new JsonResponse(200); 
		$JsonResponse->response(array("msj"=>"Se guardo correctamente el trabajo en la bitacora"));
    } 
    else 
    { 
    		errorResponse("No se pudo guardar el trabajo",404);
}

	function errorResponse($msj,$code=404)
{
	$JsonResponse= new JsonResponse(); 
	$JsonResponse->errorResponse($msj,$code);
	die();

}
?>	
