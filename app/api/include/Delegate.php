<?php

require_once "Database.php";

class Delegate {

  private $mixId;
  private $table;

  private $db;

  function __construct() {
    $this->db = new Database();
  }

  private function getOldServer() {
    $mixminion = $this->db->select(
      "SELECT minionId FROM {$this->table} WHERE mixId=? LIMIT 1",
      array($this->mixId),
      array("%d")
    );

    $mixminion = $mixminion + array(null);
    $minionId = $mixminion[0]["minionId"];

    if (!empty($minionId)) {
      $minion = $this->db->select(
        "SELECT minionRoot FROM minions WHERE minionId=?",
        array($minionId),
        array("%d")
      );

      return $minion[0]["minionRoot"];
    } else {
      return false;
    }
  }

  private function getNewServer() {
    // Shitty load balancing.

    $minions = $this->db->query("SELECT * FROM `minions` WHERE `load`=0");

    if (empty($minions[0]["minionId"])) {
      $this->db->simpleQuery("UPDATE `minions` SET `load`=0");
      $minion = $this->db->query("SELECT * FROM `minions` ORDER BY RAND() LIMIT 1");
      $minion = $minion[0];
    } else {
      $minion = $minions[array_rand($minions)];
    }

    $minionId = $minion["minionId"];

    $this->db->simpleQuery("UPDATE `minions` SET `load`=1 WHERE `minionId`={$minionId}");

    $this->db->update(
      $this->table,
      array("minionId" => $minionId),
      array("%d"),
      array("mixId" => $this->mixId),
      array("%d")
    );

    return $minion["minionRoot"];
  }

  function usingminions() {
    return (!empty(Config::$minions));
  }

  function verifyServer($server) {
    $minion = $this->db->select(
      "SELECT minionId FROM minions WHERE minionRoot=? LIMIT 1",
      array($server),
      array("%s")
    );

    return (!empty($minion[0]["minionId"]));
  }

  function getServer($mixId, $table) {
    $this->mixId = $mixId;
    $this->table = $table;

    $server = $this->getOldServer();

    if ($server === false) {
      $server = $this->getNewServer();
    }

    return $server;
  }

}
