<?php 
/**
 * Clase abtracta usada como enum , para tener tipos de datos relacionados
 * Indica el tipo de archivo de confihiracion .ini , .yaml, .php
 */
namespace Lindan\Data;
abstract class ConfigurationFileType
{
    const INI = 0;
    const YAML = 1;
    const PHP= 3;
    // etc.
}
class DatabaseBuilder {
	const CONFIGURATION_PATH="";
	public function build($type= 0)
	{
		if($type==ConfigurationFileType::INI){

		}
	}
	/*
		obtiene la configuracion de un archivo INI
	 */
	public function getConfigurationFromIniFile($value='')
	{
		$array=yaml_parse();
	}
}

?>