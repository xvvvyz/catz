<?php

(PHP_SAPI !== "cli" || isset($_SERVER["HTTP_USER_AGENT"])) && die();

include "../api/include/Database.php";

$db = new Database();

// Slaves tables.

$db->simpleQuery("CREATE TABLE `slaves` (
`slaveId` int(11) unsigned NOT NULL AUTO_INCREMENT,
`slaveRoot` tinyblob NOT NULL,
`load` int(11) NOT NULL,
PRIMARY KEY (`slaveId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;");

// 8tracks tables.

$db->simpleQuery("CREATE TABLE `8tracks_playlists` (
`mixId` tinyblob NOT NULL,
`totalTracks` int(11) NOT NULL,
`playToken` int(11) DEFAULT NULL,
`lastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`slaveId` int(11) DEFAULT NULL,
PRIMARY KEY (`mixId`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db->simpleQuery("CREATE TABLE `8tracks_songs` (
`songId` tinyblob NOT NULL,
`title` tinyblob NOT NULL,
`artist` tinyblob,
`album` tinyblob,
`duration` int(11) NOT NULL,
`songUrl` varchar(2083) NOT NULL DEFAULT '',
PRIMARY KEY (`songId`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db->simpleQuery("CREATE TABLE `8tracks_playlists_songs` (
`mixId` tinyblob NOT NULL,
`songId` int(11) NOT NULL,
`trackNumber` tinyblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
