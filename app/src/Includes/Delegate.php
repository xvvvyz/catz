<?php

namespace Omgcatz\Includes;

/**
 * manage minion download servers
 */
class Delegate {

  /**
   * we need this to make sure we use the same server every time for a given mix
   * @var string
   */
  private $mixId;

  /**
   * where are we looking?
   * @var string
   */
  private $table;

  /**
   * @var Database
   */
  private $db;

  public function __construct(Database $db) {
    $this->db = $db;
  }


  /**
   * determine if we are going to use minion servers
   * @return boolean
   */
  public function usingMinions() {

    $minionCount = $this->db->query('SELECT COUNT(*) as minionCount FROM minions');

    return (int)$minionCount[0]['minionCount'] !== 0;
  }

  /**
   * if a minion server is already set for a given mix, use that
   * @return mixed
   */
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

  /**
   * figure out which minion server we are going to use for this mix
   * @return string
   * @todo actually have some kind of algorithm that selects the best server
   */
  private function getNewServer() {
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

  /**
   * make sure the server exists before using it
   * @param string $server
   * @return boolean
   */
  public function verifyServer($server) {
    $minion = $this->db->select(
      "SELECT minionId FROM minions WHERE minionRoot=? LIMIT 1",
      array($server),
      array("%s")
    );

    return (!empty($minion[0]["minionId"]));
  }

  /**
   * give em the server we are going to use
   * @param string $mixId
   * @param string $table
   * @return string
   */
  public function getServer($mixId, $table) {
    $this->mixId = $mixId;
    $this->table = $table;

    $server = $this->getOldServer();

    if ($server === false) {
      $server = $this->getNewServer();
    }

    return $server;
  }

}
