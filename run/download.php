<?php
	// get directory name
	$pathParts = pathinfo($_GET['p']);
	$dirName  = $pathParts['dirname'];

	// prevent any file from being downloaded
	if (!preg_match('|^archives/[^/]+$|', $dirName) && $dirName != "songs") {
		exit(1);
	}

	$fileSize = filesize($_GET['p']);
	header('Content-Length: '.$fileSize);
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: attachment; filename="'.$_GET['t'].'"');
	readfile($_GET['p']);
?>