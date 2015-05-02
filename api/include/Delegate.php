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

    $mixSlave = $mixSlave + array(null);
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
    // Shitty load balancing.

    $slaves = $this->db->query("SELECT * FROM `slaves` WHERE `load`=0");

    if (empty($slaves[0]["slaveId"])) {
      $this->db->simpleQuery("UPDATE `slaves` SET `load`=0");
      $slave = $this->db->query("SELECT * FROM `slaves` ORDER BY RAND() LIMIT 1");
      $slave = $slave[0];
    } else {
      $slave = $slaves[array_rand($slaves)];
    }

    $slaveId = $slave["slaveId"];

    $this->db->simpleQuery("UPDATE `slaves` SET `load`=1 WHERE `slaveId`={$slaveId}");

    $this->db->update(
      $this->table,
      array("slaveId" => $slaveId),
      array("%d"),
      array("mixId" => $this->mixId),
      array("%d")
    );

    return $slave["slaveRoot"];
  }

  function usingSlaves() {
    return (!empty(Config::$slaves));
  }

  function verifyServer($server) {
    $slave = $this->db->select(
      "SELECT slaveId FROM slaves WHERE slaveRoot=? LIMIT 1",
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
