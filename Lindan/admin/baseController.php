<?php 

	include_once 'CookieManager.php';
	include_once ("SessionManager.php");
	$controller="";
	$pozo="";
	$usuarioNombre="";
	$urlPozoIcono="img/icono.png";
	$mensajeCabezera="";
	$isLogged=false; 
	$CookieManager = new CookieManager();
	if($CookieManager->tienePozo()){
		$pozo=$CookieManager->getPozo();
	}else{
		$pozo="San carlos";
	}

	//sesion:
	$sessionManager = new SessionManager();
	if($sessionManager->estaAutenticado()){
		$usuarioNombre=$sessionManager->getNombreUsuario();
		$isLogged=true; 
	}
	//echo $usuarioNombre;

	if(isset($_REQUEST['pozoid'])){
		$basePozoId=$_REQUEST['pozoid'];
		switch ($basePozoId) {
			case 1:
					$pozo="San carlos";
				break;
			case 2:
					$pozo="Chicola";
					break;
			case 3:
					$pozo="Simon";
					break;
			default:
				# code...
				break;
		}
	}
 ?>