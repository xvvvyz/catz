<?php

include "Config.php";

class Database extends Config {

  // Output object.
  protected $output;

  // Database connection.
  private $connection;


  /**
   * Constructor.
   * @param object $output
   */
  function __construct($output) {
    // Set output object.
    $this->output = $output;

    // Connect to database.
    $this->connection = new mysqli(
      $this->server,
      $this->user,
      $this->password,
      $this->database
    );

    if ($this->connection->connect_errno) {
      $this->output->error("Failed to connect to MySQL: (".$this->connection->connect_errno.") ".$this->connection->connect_error.".");
    }
  }

  /**
   * Destructor.
   */
  function __destruct() {
    $this->connection->close();
  }

  /**
   * Run MySQL query.
   * @param string $query
   * @return mixed
   */
  function query($query) {
    $results = $this->connection->query($query);

    if (!$results) {
      $this->output->error("MySQL query failed: (".$this->connection->errno.") ".$this->connection->error.".");
    }

    return $results;
  }

  /**
   * Fetch one row from query.
   * @param string $query
   * @return mixed
   */
  function fetchRows($query) {
    return $this->query($query)->fetch_array();
  }

  /**
   * Get number of rows from query.
   * @param string $query
   * @return mixed
   */
  function numRows($query) {
    return $this->query($query)->num_rows;
  }

  /**
   * Check if table exists in DB.
   * @param string $tableName
   * @return boolean
   */
  function tableExists($tableName) {
    return ($this->numRows("SHOW TABLES LIKE '".$tableName."'") > 0);
  }
}
