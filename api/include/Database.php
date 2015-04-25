<?php

require_once "Config.php";

class Database {

  protected function connect() {
    return new mysqli(Config::$server, Config::$user, Config::$password, Config::$database);
  }

  public function query($query) {
    $db = $this->connect();
    $result = $db->query($query);
    
    $results = array();
    while ($row = $result->fetch_object()) {
      $results[] = $row;
    }
    
    return $results;
  }

  public function insert($table, $data, $format) {
    // Check for $table or $data not set.
    if (empty($table) || empty($data)) {
      return false;
    }
    
    // Connect to the database.
    $db = $this->connect();
    
    // Cast $data and $format to arrays.
    $data = (array) $data;
    $format = (array) $format;
    
    // Build format string.
    $format = implode('', $format); 
    $format = str_replace('%', '', $format);
    
    list($fields, $placeholders, $values) = $this->prepQuery($data);
    
    // Prepend $format onto $values.
    array_unshift($values, $format); 

    // Prepary our query for binding.
    $stmt = $db->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})");

    // Dynamically bind values.
    call_user_func_array(array($stmt, 'bind_param'), $this->refValues($values));
    
    // Execute the query.
    $stmt->execute();
    
    // Check for successful insertion.
    if ($stmt->affected_rows) {
      return true;
    }
    
    return false;
  }

  public function update($table, $data, $format, $where, $whereFormat) {
    // Check for $table or $data not set.
    if (empty($table) || empty($data)) {
      return false;
    }
    
    // Connect to the database.
    $db = $this->connect();
    
    // Cast $data and $format to arrays.
    $data = (array) $data;
    $format = (array) $format;
    
    // Build format array.
    $format = implode('', $format); 
    $format = str_replace('%', '', $format);
    $whereFormat = implode('', $whereFormat); 
    $whereFormat = str_replace('%', '', $whereFormat);
    $format .= $whereFormat;
    
    list($fields, $placeholders, $values) = $this->prepQuery($data, 'update');
    
    //Format where clause.
    $whereClause = '';
    $whereValues = '';
    $count = 0;
    
    foreach ($where as $field => $value) {
      if ($count > 0) {
        $whereClause .= ' AND ';
      }
      
      $whereClause .= $field . '=?';
      $whereValues[] = $value;
      
      $count++;
    }

    // Prepend $format onto $values.
    array_unshift($values, $format);
    $values = array_merge($values, $whereValues);

    // Prepary our query for binding.
    $stmt = $db->prepare("UPDATE {$table} SET {$placeholders} WHERE {$whereClause}");
    
    // Dynamically bind values.
    call_user_func_array(array($stmt, 'bind_param'), $this->refValues($values));
    
    // Execute the query.
    $stmt->execute();
    
    // Check for successful insertion.
    if ($stmt->affected_rows) {
      return true;
    }
    
    return false;
  }

  public function select($query, $data, $format) {
    // Connect to the database.
    $db = $this->connect();
    
    //Prepare our query for binding.
    $stmt = $db->prepare($query);
    
    //Normalize format.
    $format = implode('', $format);
    $format = str_replace('%', '', $format);
    
    // Prepend $format onto $values.
    array_unshift($data, $format);
    
    //Dynamically bind values.
    call_user_func_array(array($stmt, 'bind_param'), $this->refValues($data));
    
    //Execute the query.
    $stmt->execute();
    
    //Fetch results.
    $result = $stmt->get_result();
    
    //Create results array.
    $results = array();

    while ($row = $result->fetch_assoc()) {
      $results[] = $row;
    }

    return $results;
  }

  public function delete($table, $id) {
    // Connect to the database.
    $db = $this->connect();
    
    // Prepary our query for binding.
    $stmt = $db->prepare("DELETE FROM {$table} WHERE ID = ?");
    
    // Dynamically bind values.
    $stmt->bind_param('d', $id);
    
    // Execute the query.
    $stmt->execute();
    
    // Check for successful insertion.
    if ($stmt->affected_rows) {
      return true;
    }
  }

  private function prepQuery($data, $type='insert') {
    // Instantiate $fields and $placeholders for looping.
    $fields = '';
    $placeholders = '';
    $values = array();
    
    // Loop through $data and build $fields, $placeholders, and $values.
    foreach ($data as $field => $value) {
      $fields .= "{$field},";
      $values[] = $value;
      
      if ($type == 'update') {
        $placeholders .= $field . '=?,';
      } else {
        $placeholders .= '?,';
      }
    }
    
    // Normalize $fields and $placeholders for inserting.
    $fields = substr($fields, 0, -1);
    $placeholders = substr($placeholders, 0, -1);
    
    return array($fields, $placeholders, $values);
  }

  private function refValues($array) {
    $refs = array();

    foreach ($array as $key => $value) {
      $refs[$key] = &$array[$key]; 
    }

    return $refs; 
  }

}
