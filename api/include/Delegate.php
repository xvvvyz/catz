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
    $mixSlave = $this->db->select(
      "SELECT slaveId FROM {$this->table} WHERE mixId=? LIMIT 1",
      array($this->mixId),
      array("%d")
    );

    $slaveId = $mixSlave[0]["slaveId"];

    if (!empty($slaveId)) {
      $slave = $this->db->select(
        "SELECT slaveRoot FROM slaves WHERE slaveId=?",
        array($slaveId),
        array("%d")
      );

      return $slave[0]["slaveRoot"];
    } else {
      return false;
    }
  }

  private function getNewServer() {
    // TODO: currently we are selecting a random slave, this should be a fancy algorithm.

    $slave = $this->db->query("SELECT slaveId,slaveRoot FROM `slaves` ORDER BY RAND() LIMIT 1");

    $this->db->update(
      $this->table,
      array("slaveId" => $slave[0]["slaveId"]),
      array("%d"),
      array("mixId" => $this->mixId),
      array("%d")
    );

    return $slave[0]["slaveRoot"];
  }

  function usingSlaves() {
    return (!empty(Config::$slaves));
  }

  function verifyServer($server) {
    $slave = $this->db->select(
      "SELECT slaveId FROM `slaves` WHERE slaveRoot=? LIMIT 1",
      array($server),
      array("%s")
    );

    return (!empty($slave[0]["slaveId"]));
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
