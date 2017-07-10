<?php 

function issetAndNotNull($var){
	//return (isset($var)&&!empty($var));
	return (isset($var));
}
function isNotSetOrEmpty($value)
{
	return (!isset($value)||empty($value));
}

 ?>