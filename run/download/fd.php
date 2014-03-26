<?php
/* this script runs fetch_download.sh */

$songTitle   = escapeshellarg(html_entity_decode($_POST["songTitle"]));
$tagTitle    = escapeshellarg(html_entity_decode($_POST["tagTitle"]));
$songArtist  = escapeshellarg(html_entity_decode($_POST["songArtist"]));
$songAlbum   = escapeshellarg(html_entity_decode($_POST["songAlbum"]));
$songImg     = escapeshellarg($_POST["songImg"]);
$trackNumber = escapeshellarg($_POST["trackNumber"]);
$totalTracks = escapeshellarg($_POST["totalTracks"]);
$songUrl     = escapeshellarg($_POST["songUrl"]);
$mixSlug     = escapeshellarg($_POST["mixSlug"]);
$recursive   = escapeshellarg($_POST["recursive"]);
$downloadId  = escapeshellarg($_POST["downloadId"]);
$songId      = escapeshellarg($_POST["songId"]);

$output = shell_exec("./fetch_download.sh $songTitle $tagTitle $songArtist $songAlbum $songImg $trackNumber $totalTracks $songUrl $mixSlug $recursive $downloadId $songId");
echo json_encode(array($output));
