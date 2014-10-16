<?php

include "DatabaseLogin.php";

class Database extends DatabaseLogin {

  // Debugging
  protected $debugging = true;

  // Output object.
  protected $output;

  // Database connection.
  private $connection;

  /**
   * Constructor.
   */
  function __construct($output) {
    if ($this->debugging) {
      // Development.
      ini_set("display_errors", "On");
      error_reporting(E_ALL | E_STRICT);
    } else {
      // Production.
      ini_set("display_errors", "Off");
      error_reporting(0);
    }

    // Set output object.
    $this->output = $output;

    // Connect to database.
    $this->connection = mysqli_connect(
      $this->server,
      $this->user,
      $this->password,
      $this->database
    );

    // Check connection.
    if (mysqli_connect_errno()) {
      $this->output->error("Failed to connect to MySQL: ".mysqli_connect_error().".");
    }
  }

  /**
   * Destructor.
   */
  function __destruct() {
    // Close connection to database.
    mysqli_close($this->connection);
  }

  /**
   * Run MySQL query.
   * @param string $query
   * @return mixed
   */
  function query($query) {
    if ($this->debugging) {
      $results = mysqli_query($this->connection, $query);

      if (!$results) {
        die(mysqli_error($this->connection));
      }

      return $results;
    } else {
      return mysqli_query($this->connection, $query);
    }
  }

  /**
   * Check if table exists in DB.
   * @param string $tableName
   * @return boolean
   */
  function tableExists($tableName) {
    if (mysqli_num_rows($this->query("SHOW TABLES LIKE '".$tableName."'")) > 0) {
      return true;
    } else {
      return false;
    }
  }
}
