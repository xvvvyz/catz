<?php

namespace Omgcatz\Includes;

use mysqli;

/**
 * mysql crud with prepared statements.
 */
class Database
{
    /**
   * @var string
   */
  private $server;

  /**
   * @var string
   */
  private $user;

  /**
   * @var string
   */
  private $password;

  /**
   * @var string
   */
  private $database;

  /**
   * @param string $server
   * @param string $user
   * @param string $password
   * @param string $database
   */
  public function __construct($server, $user, $password, $database)
  {
      $this->server = $server;
      $this->user = $user;
      $this->password = $password;
      $this->database = $database;
  }

  /**
   * connect to db and return db object.
   *
   * @return mysqli
   */
  private function connect()
  {
      return new mysqli($this->server, $this->user, $this->password, $this->database);
  }

  /**
   * prep a query.
   *
   * @param array $data
   * @param string $type
   *
   * @return array
   */
  private function prepQuery($data, $type = 'insert')
  {
      // instantiate $fields and $placeholders for looping
    $fields = '';
      $placeholders = '';
      $values = array();

    // loop through $data and build $fields, $placeholders, and $values
    foreach ($data as $field => $value) {
        $fields .= "{$field},";
        $values[] = $value;

        if ($type == 'update') {
            $placeholders .= $field.'=?,';
        } else {
            $placeholders .= '?,';
        }
    }

    // normalize $fields and $placeholders for inserting
    $fields = substr($fields, 0, -1);
      $placeholders = substr($placeholders, 0, -1);

      return array($fields, $placeholders, $values);
  }

  /**
   * turn array values into references.
   *
   * @param array $array
   *
   * @return array
   */
  private function refValues($array)
  {
      $refs = array();

      foreach ($array as $key => $value) {
          $refs[$key] = &$array[$key];
      }

      return $refs;
  }

  /**
   * execute a query and return array.
   *
   * @param string $query
   *
   * @return array
   */
  public function query($query)
  {
      $db = $this->connect();
      $result = $db->query($query);

      $results = array();
      while ($row = $result->fetch_assoc()) {
          $results[] = $row;
      }

      return $results;
  }

  /**
   * execute a query and return mysql result.
   *
   * @param string $query
   *
   * @return object
   */
  public function simpleQuery($query)
  {
      $db = $this->connect();
      $result = $db->query($query);

      return $result;
  }

  /**
   * insert data into db.
   *
   * @param string $table
   * @param array $data
   * @param array $format
   *
   * @return bool
   */
  public function insert($table, $data, $format)
  {
      // check for $table or $data not set
    if (empty($table) || empty($data)) {
        return false;
    }

    // connect to the database
    $db = $this->connect();

    // cast $data and $format to arrays
    $data = (array) $data;
      $format = (array) $format;

    // build format string
    $format = implode('', $format);
      $format = str_replace('%', '', $format);

      list($fields, $placeholders, $values) = $this->prepQuery($data);

    // prepend $format onto $values
    array_unshift($values, $format);

    // prepary our query for binding
    $stmt = $db->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})");

    // dynamically bind values
    call_user_func_array(array($stmt, 'bind_param'), $this->refValues($values));

    // execute the query
    $stmt->execute();

    // check for successful insertion
    if ($stmt->affected_rows) {
        return true;
    }

      return false;
  }

  /**
   * update data in db.
   *
   * @param string $table
   * @param array $data
   * @param array $format
   * @param array $where
   * @param array $whereFormat
   *
   * @return bool
   */
  public function update($table, $data, $format, $where, $whereFormat)
  {
      // check for $table or $data not set
    if (empty($table) || empty($data)) {
        return false;
    }

    // connect to the database
    $db = $this->connect();

    // cast $data and $format to arrays
    $data = (array) $data;
      $format = (array) $format;

    // build format array
    $format = implode('', $format);
      $format = str_replace('%', '', $format);
      $whereFormat = implode('', $whereFormat);
      $whereFormat = str_replace('%', '', $whereFormat);
      $format .= $whereFormat;

      list($fields, $placeholders, $values) = $this->prepQuery($data, 'update');

    // format where clause
    $whereClause = '';
      $whereValues = '';
      $count = 0;

      foreach ($where as $field => $value) {
          if ($count > 0) {
              $whereClause .= ' AND ';
          }

          $whereClause .= $field.'=?';
          $whereValues[] = $value;

          ++$count;
      }

    // prepend $format onto $values
    array_unshift($values, $format);
      $values = array_merge($values, $whereValues);

    // prepary our query for binding
    $stmt = $db->prepare("UPDATE {$table} SET {$placeholders} WHERE {$whereClause}");

    // dynamically bind values
    call_user_func_array(array($stmt, 'bind_param'), $this->refValues($values));

    // execute the query
    $stmt->execute();

    // check for successful insertion
    if ($stmt->affected_rows) {
        return true;
    }

      return false;
  }

  /**
   * select data from db.
   *
   * @param string $query
   * @param array $data
   * @param array $format
   *
   * @return array
   */
  public function select($query, $data, $format)
  {
      // connect to the database
    $db = $this->connect();

    // prepare our query for binding
    $stmt = $db->prepare($query);

    // normalize format
    $format = implode('', $format);
      $format = str_replace('%', '', $format);

    // prepend $format onto $values
    array_unshift($data, $format);

    // dynamically bind values
    call_user_func_array(array($stmt, 'bind_param'), $this->refValues($data));

    // execute the query
    $stmt->execute();

    // fetch results
    $result = $stmt->get_result();

    // create results array
    $results = array();

      while ($row = $result->fetch_assoc()) {
          $results[] = $row;
      }

      return $results;
  }

  /**
   * delete data from db.
   *
   * @param string $table
   * @param int $id
   *
   * @return bool
   */
  public function delete($table, $id)
  {
      // connect to the database
    $db = $this->connect();

    // prepary our query for binding
    $stmt = $db->prepare("DELETE FROM {$table} WHERE ID = ?");

    // dynamically bind values
    $stmt->bind_param('d', $id);

    // execute the query
    $stmt->execute();

    // check for successful deletion
    if ($stmt->affected_rows) {
        return true;
    }

      return false;
  }
}
