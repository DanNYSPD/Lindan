
<?php
session_start();
include_once('Services/response/JsonResponse.php');
include_once 'Services/TuberiaService.php';
//include '../conexion2.php'; 

if(isset($_SESSION['user'])){
    //echo "";
}else{
    //echo '<script>window.location="Administrador.php";</script>';
    errorResponse("no estas autorizado,inicia sesion",401);
}
require '../conexion2.php';
	
	
	$Diametro = $_POST['Diametro'];
	$Material = $_POST['Material'];
	$Metros = $_POST['Metros'];
	$PozoId=$_POST["pozoid"];
	
//	$sql = "INSERT INTO tuberia (Diametro, Material, Metros) VALUES ('$Diametro', '$Material', '$Metros')";
//	$resultado = mysqli_query($mysqli, $sql);
$TuberiaService= new TuberiaService();
$resultado=$TuberiaService->agregarTuberia($Diametro,$Material,$Metros,$PozoId);
//var_dump($resultado);
 if($resultado) 
	{ 
		$JsonResponse= new JsonResponse(200); 
		$JsonResponse->response(array("msj"=>"Se guardo correctamente la tuberia en el inventario"));
    } 
    else 
    { 
    		errorResponse("No se pudo guardar el inventario",404);
}
function errorResponse($msj,$code=404)
{
	$JsonResponse= new JsonResponse($code); 
	$JsonResponse->response(array("msj"=>$msj));
	die();

}
?>