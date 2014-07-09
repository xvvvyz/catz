<?php
/* this script archives all of the files in a path */

include '../include/functions.php';

// get path parts
$pathParts  = pathinfo($_POST['path']);
$dirName    = $pathParts['dirname'];
$fileName   = $pathParts['basename'];

// prevent arbitrary files from being archived
if (!preg_match('|^archives/[^/\.]+/[^/\.]+$|', $dirName)) {
  bail_out(403, "Not acceptable.");
}

$cmd = "cd $dirName && ../../find . \! -name *.zip -exec ../../zip -0 -D -r $fileName * \; -delete";
shell_exec($cmd);
