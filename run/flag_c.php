<?php
	$flag = $_POST["flag"];
	$reblogKey = html_entity_decode($_POST["reblogKey"]);
	
	$con = mysqli_connect("localhost", "cade_cade", "while(web>0)", "cade_omgcatz");
	if (!$con) {
		$con = mysqli_connect("localhost", "root", "alpine", "omgcatz");
	}

	$query = "UPDATE cats SET flag = $flag WHERE reblogKey = \"$reblogKey\"";
	mysqli_query($con, $query);
	mysqli_close($con);
?>
