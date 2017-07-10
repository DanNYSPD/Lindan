<?php 
include_once("DateUtils.php");
class UploadImage 	
{
	private $MAXIMUM_SIZE=500000;
	/**
	 * varible con la cual indicaremos si no importa sobreeescirbir un archivo existente
	 * @var boolean
	 */
	private $overrideFile=false;

	protected $target_dir = "../img/valvulas/";

	protected $fileName="";
	protected $fileNameP="";//nombre con extension sin path

	public function getFileNameP()
	{
		if($this->overrideFile){
			return $this->fileNameP;
		}else{
			//signigica que es nuveo y debemos generar un nombre
			return $id_valvula."_".getDateString().".".$imageFileType;
		}
	}
	//usar cuando ya tengamos definido un nombre(ya debe tener extension) (y no autoGenerado)
	public function setFileNameP($fileNameP='')
	{
		$this->fileNameP=$fileNameP;
	}

	public function setFileName($fileName='')
	{
		$this->fileName=$fileName;
	}
	
	protected $key_fileToUpload;
	public function setKeyFileToUpload($key_fileToUpload='')
	{
		$this->key_fileToUpload=$key_fileToUpload;
	}
	function __construct($fileNameP="")
	{
		if($fileNameP!=""){
			$this->setFileNameP($fileNameP);
			$this->overrideFile=true;
		}
	}

	public function isActualImage()
	{

	$check = getimagesize($_FILES[$this->key_fileToUpload]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        return true;
    } else {
        echo "File is not an image.";
        return false;
    }
	}

	public function hasValidSize($key_fileToUpload=-1)
	{
		if($key_fileToUpload==-1)
			return false;
		if ($_FILES[$key_fileToUpload]["size"] > 500000) {
		    echo "Sorry, your file is too large.";
		    $uploadOk = 0;
		}
	}

	public function isValidType($imageFileType='')
	{
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" ) {
			    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			    $uploadOk = 0;
		}
	}
	
	public function existsFileInTemp()
	{
		return (file_exists($_FILES[$this->key_fileToUpload]["tmp_name"]));

	}
	/**
	 * movueve el archivo en tmp a la ruta especificada
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function uploadFile()
	{
		if(!$this->existsFileInTemp()){

			echo "No se recibio la imagen";
			return false;
		}

		if($this->isActualImage()){
			$this->setFileName($this->target_dir.$this->getFileNameP());
			echo "echo fileName;";
			echo $this->fileName;
			echo "--";
			return $this->moveUploadedFile();
		}
		//si es ovveriride, concatenamos el path + el nombre del archivo
		

	}
	private function moveUploadedFile()
	{
		return move_uploaded_file($_FILES[$this->key_fileToUpload]["tmp_name"], $this->fileName);
	}
	public function getExtension(){
		//obtenemos el nombre + el path (este nombre no es el definitivo)
		$target_file= $this->target_dir . basename($_FILES[$this->key_fileToUpload]["name"]);
		//retornamos la extension
		return pathinfo($target_file,PATHINFO_EXTENSION);

	}
}

 ?>