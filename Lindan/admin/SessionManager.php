<?php 

	include_once("Session/Session.php");
	const LOGIN_PAGE="Administrador.php";
	const USER_ID = "user";
	class SessionManager{
		

		private $session;
		


		private $userName=null;



		function __construct($foo = null)
		{
		
			$this->foo = $foo;
			$this->session=new Session();
		}

		public function getNombreUsuario()
		{
			return $this->userName;
		}
		public function setNombreUsuario($userName)
		{
			 $this->userName=$userName;
		}
		/**
		 * verificar si el suaurio esta autenticado
		 * @return [type] [description]
		 */
		public function estaAutenticado()
		{
			$this->session->start();
			$res=$this->session->get(USER_ID);
			if(!$res)
				return false;	
			else{
				$this->userName=$res;
				return true;
			}
		}
		/*
			redirige a la pagina login
		 */
		public function redirigirALogin($page=null)

		{
			if($page==null){	
			  echo '<script>window.location="'.LOGIN_PAGE.'";</script>';
			 }else{
			 	echo '<script>window.location="'.$page.'";</script>';
			 }
		}
		/**
		 * valida si esta autenticado, en caso de no estarlo regirige a la loginPage
		 * @return [type] [description]
		 */
		public function validacionAutomatica($page=null){
			if(!$this->estaAutenticado()){
				$this->redirigirALogin($page);
			}
		}

	}


 ?>