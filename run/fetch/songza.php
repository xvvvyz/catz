<?php
include '../include/functions.php';

$url = (isset($_POST["url"]) ? $_POST["url"] : "http://songza.com/listen/today-s-trap-and-moombahton-rump-shakers-songza/");
$stationId = (isset($_POST["stationId"]) ? $_POST["stationId"] : "");
$sessionId = (isset($_POST["sessionId"]) ? $_POST["sessionId"] : "IPAmNpAX02YTL8CPgc");
$userAgent = (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:26.0) Gecko/20100101 Firefox/26.0");

// if stationId is not set then get it along with playlist info
if (empty($stationId)) {

	// get station id from page source
	$c = curl_init($url);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($c, CURLOPT_USERAGENT, $userAgent);
	$source = curl_exec($c);
	curl_close($c);

	preg_match_all('/data-sz-station-id="[^"]*/', $source, $matches);
	list(,$stationId) = explode('"', $matches[0][0]);

	// get playlist info
	$c = curl_init("http://songza.com/api/1/station/".$stationId);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	$jsonArray = json_decode(curl_exec($c), true);
	curl_close($c);

	$stationSlug = trim($jsonArray['dasherized_name']);
	$stationName = trim($jsonArray['name']);
	$creatorName = trim($jsonArray['creator_name']);
	$songCount = trim($jsonArray['song_count']);

	// throw it into an array to add to output json
	$stationArray = array("station_id" => $stationId,
						  "station_slug" => $stationSlug,
						  "station_name" => $stationName,
						  "station_100" => "http://songza.com/api/1/station/".$stationId."/image?size=133",
						  "station_500" => "http://songza.com/api/1/station/".$stationId."/image?size=500&name=image.jpg",
						  "creator_name" => $creatorName,
						  "total_tracks" => $songCount);
}


// get a song (hopefully it's a new one) from the station
$ch = curl_init("http://songza.com/api/1/station/".$stationId."/next?cover_size=g&format=mp3");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, "sessionid=".$sessionId."; visitor-prompted:1");
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
curl_exec($ch);
$songArray = json_decode(curl_exec($ch), true);
curl_close($ch);

$add = array("error" => 0);

if (isset($stationArray)) {
	$output = json_encode(array_merge($stationArray, $songArray, $add));
} else {
	$output = json_encode(array_merge($songArray, $add));
}

echo $output;
