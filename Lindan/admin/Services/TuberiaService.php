<?php 
include_once("data/MysqlConection.php");
class TuberiaService{
	private $pozoId;
	public function setPozoId($pozoId=-1)
	{
		$this->pozoId=$pozoId;	
	}
	public function getPozoId()
	{
		return $this->pozoId;
	}
	//este es el que se invoca realmente
	public function getTuberiasPorIdLike($tuberiaId='')
	{
		if(empty($this->pozoId)||$this->pozoId==-1){	
		$and=" and pozoId=".$this->getPozoId();
		
		 $sql = "SELECT * FROM tuberia $where";
		}else{
			$where="where Id_tuberia like '".$tuberiaId."%' and pozoId=".$this->getPozoId();
			$sql = "SELECT * FROM tuberia $where";
		}
		 return $this->query($sql);
		

	}
	public function getTuberiaPorMaterial($tuberiaMaterial='')
	{
		$where="where Material='".$tuberiaMaterial."'";
		 $sql = "SELECT * FROM tuberia $where";		
		 return $this->query($sql);
	}
	public function getTuberiaPorTuberiaIdLikeAndMaterial($tuberiaId,$tuberiaMaterial)
	{
		if(empty($this->pozoId)||$this->pozoId==-1){	
		  $where="where  Id_tuberia like '".$tuberiaId."%' and Material='".$tuberiaMaterial."'";
		  $sql = "SELECT * FROM tuberia $where";
		   
		}else{
			$where="where  Id_tuberia like '".$tuberiaId."%' and Materiallike'".$tuberiaMaterial."'%"." pozoId=".$this->pozoId;
		  $sql = "SELECT * FROM tuberia $where";
		  
		}
		  return $this->query($sql);
		
	}
	public function getAllTuberias()
	{
		if(empty($this->pozoId)||$this->pozoId==-1){	
		 $sql = "SELECT * FROM tuberia";
		 //echo "sin id";
		}else{
			$sql="SELECT * FROM tuberia where pozoId=".$this->pozoId;
			//echo "con id".$this->pozoId;
		}

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
	public function agregarTuberia($Diametro,$Material,$Metros,$pozoId)
	{

		$sql = "INSERT INTO tuberia (Diametro, Material, Metros,pozoId) VALUES ('$Diametro', '$Material', '$Metros',$pozoId)";

		$db= new MysqlConnection();
		$res =$db->command($sql);
		return $res;
	}
}

 ?>
