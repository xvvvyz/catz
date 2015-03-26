<?php

(PHP_SAPI !== "cli" || isset($_SERVER["HTTP_USER_AGENT"])) && die();

include "../api/include/Output.php";
include "../api/include/Database.php";

$db = new Database(new OutputArray());

// Slaves tables.

if (!$db->tableExists("slaves")) {
  $db->query("CREATE TABLE `slaves` (
    `slaveId` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `domain` tinyblob NOT NULL,
    `load` int(11) NOT NULL,
    PRIMARY KEY (`slaveId`)
    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;");
}

// Cat table.

if (!$db->tableExists("cats")) {
  
}

// 8tracks tables.

if (!$db->tableExists("8tracks_playlists")) {
  $db->query("CREATE TABLE `8tracks_playlists` (
    `mixId` tinyblob NOT NULL,
    `totalTracks` int(11) NOT NULL,
    `playToken` int(11) DEFAULT NULL,
    `lastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `slaveId` int(11) DEFAULT NULL,
    PRIMARY KEY (`mixId`(255))
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$db->tableExists("8tracks_songs")) {
  $db->query("CREATE TABLE `8tracks_songs` (
    `songId` tinyblob NOT NULL,
    `title` tinyblob NOT NULL,
    `artist` tinyblob,
    `album` tinyblob,
    `duration` int(11) NOT NULL,
    `songUrl` varchar(2083) NOT NULL DEFAULT '',
    PRIMARY KEY (`songId`(255))
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$db->tableExists("8tracks_playlists_songs")) {
  $db->query("CREATE TABLE `8tracks_playlists_songs` (
    `mixId` tinyblob NOT NULL,
    `songId` int(11) NOT NULL,
    `trackNumber` tinyblob NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}
