<?php 
/**
 * Agrega zonas de abastecimiento
 */
include_once 'Services/LotesPorHabitantesService.php';
include_once 'Services/response/JsonResponse.php';
include_once 'Utils/ValidatorUtils.php';
include_once 'SessionManager.php';

$SessionManager= new SessionManager();
if(!$SessionManager->estaAutenticado()){
	errorResponse("no esta autorizado",401);
}
$msj;
if(isNotSetOrEmpty($_POST['pozoid'])){
	$msj="Error falta de la pozoid";
	errorResponse($msj);
}
if(isNotSetOrEmpty($_POST['Num_lote'])){
	$msj="Error falta de la manzana";
	errorResponse($msj);
}
if(isNotSetOrEmpty($_POST['Num_hab'])){
	$msj="Error falta de la manzana";
	errorResponse($msj);
}
if(isNotSetOrEmpty($_POST['Colonia'])){
	$msj="Error falta de la manzana";
	errorResponse($msj);
}
	$pozoId = $_POST['pozoid'];
	$Num_lote = $_POST['Num_lote'];
	$Num_hab = $_POST['Num_hab'];
	$Colonia = $_POST['Colonia'];

$LotesPorHabitantesService= new LotesPorHabitantesService();
$res=$LotesPorHabitantesService->agregarLote($Num_lote,$Num_hab,$Colonia,$pozoId);	
if($res==true){
	$JsonResponse= new JsonResponse(200); 
	$JsonResponse->response(array("msj"=>"Se agrego el lote"));

}else{
	var_dump($res);
}

	//var_dump($res);
 function agregarLote($value='')
{
	$LotesPorHabitantesService = new LotesPorHabitantesService();	
}
function errorResponse($msj,$code=404)
{
	$JsonResponse= new JsonResponse($code); 
	$JsonResponse->response(array("msj"=>$msj));
	die();

}

 ?>