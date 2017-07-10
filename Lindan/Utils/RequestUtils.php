<?php 
function isRequestPost()
{
	return $_SERVER['REQUEST_METHOD'] === 'POST';
}
//checa si el metodo es get
function isRequestGet()
{
	return $_SERVER['REQUEST_METHOD'] === 'GET';
}
 ?>