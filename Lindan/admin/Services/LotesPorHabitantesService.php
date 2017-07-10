<?php 
include_once("data/MysqlConection.php");
class LotesPorHabitantesService{
	private $pozoId;

	public function setPozoId($pozoId=-1)
	{
		$this->pozoId=$pozoId;
	}

	public function getLotesPorHabitantesPorManzana($ManzanaId=-1)
	{
		$sql="";
		if(empty($this->pozoId)||$this->pozoId==-1){	
		$where="where Id_manzana like '".$ManzanaId."%'";
		$sql = "SELECT * FROM lotesxhabitantes $where";
		}else{
			$sql = "SELECT * FROM lotesxhabitantes where pozoId=".$this->pozoId." and  Id_manzana like '".$ManzanaId."%'";
		}
		return $this->query($sql);
	}
	public function getLotesPorHabitantesPorColonia($coloniaId=-1)
	{
		if(empty($this->pozoId)||$this->pozoId==-1){	
 		$where="where Colonia like '".$coloniaId."%'";
 		//$where="where Colonia='".$coloniaId."'";
		$sql = "SELECT * FROM lotesxhabitantes $where";	
		}else{
			$sql = "SELECT * FROM lotesxhabitantes where pozoId=".$this->pozoId." and Colonia like '".$coloniaId."%'";	
		}	
		//echo "col";
		return $this->query($sql);
	}
	public function getLotesPorHabitantesPorManzanaAndColonia($ManzanaId=-1,$coloniaId=-1)
	{
		if(empty($this->pozoId)||$this->pozoId==-1){	
		$where="where  Id_manzana like '".$ManzanaId."%' and  Colonia like '".$coloniaId."%'";
		$sql = "SELECT * FROM lotesxhabitantes $where";
	}else{
		$sql = "SELECT * FROM lotesxhabitantes where  pozoId=".$this->pozoId." and Id_manzana like '".$ManzanaId."%' and  Colonia like '".$coloniaId."%'";
	}	
	return $this->query($sql);
	}
	public function getAllLotesPorHabitantes()
	{
		if(empty($this->pozoId)||$this->pozoId==-1){	
			$sql = "SELECT * FROM lotesxhabitantes";
			//echo "todo";
		}else{
			$sql = "SELECT * FROM lotesxhabitantes where pozoId=".$this->pozoId;
			//echo "solo de pozoId";

		}
		return $this->query($sql);
	}

	public function agregarLote($Num_lote,$Num_hab,$Colonia,$pozoId)
	{
		$sql = "INSERT INTO lotesxhabitantes (Num_lote, Num_habitantes, Colonia,pozoId) VALUES ($Num_lote, $Num_hab, '$Colonia',$pozoId)";
		$db= new MysqlConnection();			
			return $db->command($sql);
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