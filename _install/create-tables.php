<?php

(PHP_SAPI !== "cli" || isset($_SERVER["HTTP_USER_AGENT"])) && die();

include "../api/include/Database.php";

$db = new Database();

// minion tables

$db->simpleQuery("CREATE TABLE `minions` (
`minionId` int(11) unsigned NOT NULL AUTO_INCREMENT,
`minionRoot` tinyblob NOT NULL,
`load` int(11) NOT NULL,
PRIMARY KEY (`minionId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;");

// 8tracks tables

$db->simpleQuery("CREATE TABLE `8tracks_playlists` (
`mixId` int(11) NOT NULL,
`totalTracks` int(11) NOT NULL,
`playToken` int(11) DEFAULT NULL,
`lastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`minionId` int(11) DEFAULT NULL,
PRIMARY KEY (`mixId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db->simpleQuery("CREATE TABLE `8tracks_songs` (
`songId` int(11) NOT NULL,
`title` tinyblob NOT NULL,
`artist` tinyblob,
`album` tinyblob,
`duration` int(11) NOT NULL,
`songUrl` varchar(2083) NOT NULL DEFAULT '',
PRIMARY KEY (`songId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db->simpleQuery("CREATE TABLE `8tracks_playlists_songs` (
`mixId` int(11) NOT NULL,
`songId` int(11) NOT NULL,
`trackNumber` int(11) NOT NULL,
KEY `mixId` (`mixId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
