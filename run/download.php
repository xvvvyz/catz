<?php 
	$fileSize = filesize($_GET['p']);
	header('Content-Length: '.$fileSize);
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: attachment; filename="'.$_GET['t'].'"');
	readfile($_GET['p']);
?>