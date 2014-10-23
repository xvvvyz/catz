<?php

include "DatabaseLogin.php";

class Database extends DatabaseLogin {

  // Output object.
  protected $output;

  // Database connection.
  private $connection;
  private $stmt;

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
    // Check connection.
    if ($this->connection->connect_errno) {
      $this->output->error("Failed to connect to MySQL: (".$this->connection->connect_errno.") ".$this->connection->connect_error.".");
    }

    $results = $this->connection->query($query);
    
    if ($this->connection->connect_errno) {
      $this->output->error("MySQL query failed: (".$this->connection->connect_errno.") ".$this->connection->connect_error.".");
    }

    return $results;
  }

  /**
   * Fetch one row from query.
   * @param string $query
   * @return mixed
   */
  function fetchRow($query) {
    return $this->query($query)->fetch_array;
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
