<?php 
include_once("Services/PozosService.php");	

	$pozosService= new PozosService();
	
	$response= new JsonResponse();
	$response->Response($pozosService->consultarPozos());


 ?>