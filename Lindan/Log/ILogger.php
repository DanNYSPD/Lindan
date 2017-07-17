<?php 
/**
 * @author Daniel j Hernandez <[<email address>]>
 * interfaz usada para definir un logger (Para e-mail, file, etc)
 * definiciones de nombres badados en log4php para mantener compatibilidad
 */
interface ILogger {
	public function trace($message='',$throwable = null);
	public function debug($message='',$throwable = null);
	public function info($message='',$throwable = null);
	public function warn($message='',$throwable = null);
	public function error($message='',$throwable = null);
	public function fatal($message='',$throwable = null);
	public function log($message='',$throwable = null);


}
interface ILoggerConfigurator{
	public function setPath($path);	
}

 ?>