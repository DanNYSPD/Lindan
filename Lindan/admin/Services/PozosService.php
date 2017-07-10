<?php 
include_once("data/MysqlConection.php");
include_once("response/JsonResponse.php");
class PozosService{


	public	function consultarPozos(){
			$sql="select id_pozo,nombre_pozo,descripcion from pozos";				
			return $this->query($sql);
		}

			//si es 0 retorna null
	private function query($sql){
			$db= new MysqlConnection();			
			$res =$db->query($sql);
			//var_dump($res);
			if($res==null) return null;	
			if(is_array($res)&&sizeof($res)==0){
				return null;
			}
			
			return $res;
	}
}


 ?>