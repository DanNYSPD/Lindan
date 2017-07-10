<?php 
include_once("Services/ValvulasService.php");	
	$key_id_pozo="id_pozo";
	$id_pozo=-1;
	if(isset($_GET[$key_id_pozo])&&!empty($_GET[$key_id_pozo])){
		$id_pozo=$_GET["id_pozo"];
		$valvulasService= new ValvulasService();
		$res=$valvulasService->consultarValvulas($id_pozo);
		//var_dump($res);
		$response= new JsonResponse();
		$response->Response($res);

	}else{
	$response=new JsonResponse(404);
	$response->setMessage("Error, falta un atributo");
	$response->Response();

	}
	


 ?>