<?php
/* this script downloads a file in the browser */

include '../include/functions.php';

// get directory name
$pathParts = pathinfo($_GET['p']);
$dirName  = $pathParts['dirname'];

// prevent arbitrary files from being downloaded
if (!preg_match('|^archives/[^/]+$|', $dirName) && $dirName != "songs") {
	bail_out(403, "Not acceptable.");
}

$fileSize = filesize($_GET['p']);
header('Content-Length: '.$fileSize);
header("Content-Type: application/octet-stream");
header('Content-Disposition: attachment; filename="'.$_GET['t'].'"');
readfile($_GET['p']);
