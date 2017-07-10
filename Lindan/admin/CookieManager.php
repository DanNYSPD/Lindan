<?php 
class CookieManager{
		
		public function tienePozo($value='')
		{
			return (isset($_COOKIE["pozo"]));
		}
		public function getPozo()
		{
			return $_COOKIE["pozo"];
		}
		public function setPozo($pozo='')
		{
			$this->setCookie("pozo",$pozo);
				
		}
		public function setCookie($clave='',$valor)
		{
			# code...
			setcookie($clave,$valor);
		}

	}
 ?>