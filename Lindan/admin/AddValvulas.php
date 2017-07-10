<?php 
include_once("Services/ValvulasService.php");	
include_once("Utils/ValidatorUtils.php");
include 'SessionManager.php';

//primero verificamos la periccion tenga el id de la peticion
$SessionManager= new SessionManager();
if(!$SessionManager->estaAutenticado()){
	$response=new JsonResponse();
	$response->errorResponse("no tienes permimos",403);
}
	const key_titulo="titulo";
	const key_cx="cx";
	const key_cy="cy";
	const key_id_pozo="id_pozo";

if(!empty($_POST[key_titulo])&&
		!empty($_POST[key_cx])&&
		!empty($_POST[key_cy])&&
		!empty($_POST[key_id_pozo])){

//	echo $_POST[key_titulo];
//echo $_POST[key_cx];
//echo $_POST[key_cy];
//echo "id:". $_POST[key_id_pozo];
	$valvulasService= new ValvulasService();
$valvulasService->agregarValvula($_POST[key_titulo],$_POST[key_cx],$_POST[key_cy],$_POST[key_id_pozo]);
		$response=new JsonResponse(200);
		$response->Response(array("msj"=>"se guardo correctamente el punto"));
	}else{
	 

	$response=new JsonResponse();
		$response->errorResponse("error en atributos",422);
		die();
}
 ?>