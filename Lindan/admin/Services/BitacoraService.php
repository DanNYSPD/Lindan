<?php 
include_once("data/MysqlConection.php");
/**
 * Obtiene los trabajos registrados
 */
class BitacoraService{

	private $pozoId;

	public function setPozoId($pozoId=-1)
	{
		$this->pozoId=$pozoId;
	}

	public function getBitacorasPorIdLike($trabajoId=-1)
	{
		if(empty($this->pozoId)||$this->pozoId==-1){	
		$where="where Id_trabajo like '".$trabajoId."%'";
		$sql = "SELECT * FROM bitacora $where";
		}else{
			$where="where Id_trabajo like '".$trabajoId."%' and pozoId=".$this->pozoId;
			$sql = "SELECT * FROM bitacora $where";
		}
		return $this->query($sql);
	}
	public function getBitacorasPorFecha($trabajoFecha='')
	{
		if(empty($this->pozoId)||$this->pozoId==-1){	
		 $where="where Fecha='".$trabajoFecha."'";
		 $sql = "SELECT * FROM bitacora $where";
		 	}else{
			 $where="where Fecha='".$trabajoFecha."' and pozoId=".$this->pozoId;
			 $sql = "SELECT * FROM bitacora $where";
		 	}
		return $this->query($sql);
	}
	public function getBitacorasPorIdAndFecha($trabajoId,$trabajoFecha='')
	{
		if(empty($this->pozoId)||$this->pozoId==-1){	
		$where="where Id_trabajo like '".$trabajoId."%' and Fecha ='".$trabajoFecha."'";
		$sql = "SELECT * FROM bitacora $where";
		}else{
			$where="where Id_trabajo like '".$trabajoId."%' and Fecha ='".$trabajoFecha."' and pozoId=".$this->pozoId;
			$sql = "SELECT * FROM bitacora $where";	
		}

		return $this->query($sql);
	}


	public function getBitacoraPorId($bitacoraId='')
	{
		$sql = "SELECT * FROM bitacora where Id_trabajo =".$bitacoraId." limit 1;";
		$db= new MysqlConnection();			
		return $db->querySingle($sql);
		
	}
	public function getAllBitacoras()
	{
		if(empty($this->pozoId)||$this->pozoId==-1){	
		$sql = "SELECT * FROM bitacora ;";
		}else{
			$sql= "SELECT * FROM bitacora where pozoId=".$this->pozoId;
		}
		return $this->query($sql);	
	}

	public function addBitacora($Fecha,$Trabajo,$Monto,$pozoId)
	{
		$sql = "INSERT INTO bitacora (Fecha, Trabajo, Monto,pozoId) VALUES ('$Fecha', '$Trabajo', '$Monto',$pozoId)";
		$db= new MysqlConnection();
		return $db->command($sql);	
	}
	public function updateBitacora($Fecha,$Trabajo,$Monto,$Id_trabajo)
	{
		$sql = "UPDATE bitacora SET Fecha='$Fecha', Trabajo='$Trabajo', Monto='$Monto' WHERE Id_trabajo= ".$Id_trabajo;
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