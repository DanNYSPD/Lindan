<?php
include_once("data/MysqlConection.php");
include_once("response/JsonResponse.php"); 
class ValvulasService{

	/**
	 * [consulta las Valvulas segun el id del pozo]
	 * @param  integer $idPozo [description]
	 * @return [type]          [description]
	 */
	public function consultarValvulas($idPozo=-1)
		{
			if($idPozo==-1)
				return null;
			$sql="select * from puntos where id_pozo =$idPozo;";

			$db= new MysqlConnection();
			
			$res =$db->query($sql);	
			if(is_array($res)&&sizeof($res)==0){
				return null;
			}
			return $res;
		}
		public function consultarValvula($idValvula=-1)
			{
				if($idValvula==-1)
				return null;
			$sql="select * from puntos where IdPunto =$idValvula limit 1;";

			$db= new MysqlConnection();
			
			$res =$db->querySingle($sql);	
			if(is_array($res)&&sizeof($res)==0){
				return null;
			}
			return $res;
			}	

	
	public function agregarValvula($titulo,$cx,$cy,$idPozo)
	{
		$sql="INSERT INTO puntos (Titulo, cx, cy,id_pozo) VALUES ('$titulo',$cx,$cy,$idPozo)";
		$db= new MysqlConnection();
		$res =$db->command($sql);
	}

	public function agregarValvulaImagen($idValvula=-1,$imagenURL,$imagenDescripcion,$imagenNombre)
	{
		if($idValvula==-1)
			return null;
		
		$sql="insert into imagenes_puntos(url_imagen,descripcion,id_punto,imagen_nombre) values('$imagenURL','$imagenDescripcion',$idValvula,'$imagenNombre')";

		$db= new MysqlConnection();
		return $db->command($sql);
		
	}
	public function getImagenesDeValvula($idValvula=-1)
	{
		if($idValvula==-1)
				return null;
		$sql="select id_Imagenes_puntos,url_imagen,descripcion,id_punto,imagen_nombre from imagenes_puntos where id_punto=$idValvula;";

		$db= new MysqlConnection();
		
		$res =$db->query($sql);	
		
		return $res;
	}

	public function getImagenDeValvulaPorId($imagenId=-1){
		if(!$imagenId||$imagenId==-1)
			return null;
		//echo $imagenId;
		$sql="select id_Imagenes_puntos,url_imagen,descripcion,id_punto,imagen_nombre from imagenes_puntos where id_Imagenes_puntos=$imagenId limit 1;";

		$db= new MysqlConnection();
		
		$res =$db->querySingle($sql);	
		
		return $res;
	}
	public function updateImagenDescripcionDeValvula($imagenId=-1,$nDescripcion="")
	{
		$sql ="update imagenes_puntos set  descripcion ='$nDescripcion' where  id_Imagenes_puntos=$imagenId";
		$db= new MysqlConnection();
		return $db->command($sql);
	}
	public function deleteValvulaImagenPorId($imagenId=-1)
	{
		
		$sql="delete from imagenes_puntos where id_Imagenes_puntos=$imagenId;";
		$db= new MysqlConnection();
		return $db->command($sql);
	}
}
?>