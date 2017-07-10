

<?php 
	function getDateString()
	{
		$dateArr=getDate();
		return $dateArr['year'].$dateArr['mon'].$dateArr['mday'].$dateArr['hours'].$dateArr['minutes'].$dateArr['seconds'];
	}

 ?>
