<?php

require_once("../config/conf.php");

class DatabaseService
{
  private $db_host = DB_HOST;
  private $db_name  = DB_NAME;
  private $db_user = DB_USER;
  private $db_password  = DB_PASSWORD;
  private $charset  = CHARSET;
  private $connection;

  private function connect()
  {
    $this->connection = null;
    try {
      $this->connection = new PDO("mysql:host=" . $this->db_host . ";dbname=" . $this->db_name . ";charset=" . $this->charset, $this->db_user, $this->db_password);
    } catch (PDOException $exception) {
      echo "Connection Failed: " . $exception->getMessage();
    }

    return $this->connection;
  }

  private function generateUniqueID()
  {
    $token = substr(md5(uniqid(rand(), true)), 0, 6);
    // Check if Token exists
    if ($this->checkIfTokenExists($token)) {
      $this->generateUniqueID();
    } else {
      return $token;
    }
  }

  private function checkIfTokenExists(string $token): bool
  {
    $con = $this->connect();
    $stmt = $con->prepare("SELECT * FROM shorts WHERE short = ? LIMIT 1,0");
    $stmt->bindParam(1, $token);
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num > 0) {
      return true;
    } else {
      return false;
    }
  }

  private function checkIfUrlExists(string $url)
  {
    $con = $this->connect();
    $stmt = $con->prepare("SELECT * FROM shorts WHERE origin = ? LIMIT 1,0");
    $stmt->bindParam(1, $url);
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num > 0) {
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      return $row["origin"];
    } else {
      return false;
    }
  }

  private function createNewEntry(string $url)
  {
    try {
      $con = $this->connect();
      $stmt = $con->prepare("INSERT INTO shorts (origin, short) VALUES (?,?)");
      $short = $this->generateUniqueID();
      $stmt->bindParam(1, $url);
      $stmt->bindParam(2, $short);
      $stmt->bindParam(3, $user);
      $stmt->execute();
      return $short;
    } catch (PDOException $ex) {
      echo $ex->getMessage();
      die;
    }
  }

  private function getCounter(int $id)
  {
    try {
      $con = $this->connect();
      $stmt = $con->prepare("SELECT count FROM shorts WHERE id=? LIMIT 1,0");
      $stmt->bindParam(1, $id);
      $stmt->execute();
      $num = $stmt->rowCount();
      if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $row["count"];
        if ($count == null) {
          return 0;
        } else {
          return $count;
        }
      }
    } catch (PDOException $ex) {
      echo $ex->getMessage();
      die(500);
    }
  }

  private function updateCounter(int $id)
  {
    $count = $this->getCounter($id);
    try {
      $con = $this->connect();
      $stmt = $con->prepare("UPDATE shorts SET count = ? WHERE id=?");
      $stmt->bindParam(1, $count);
      $stmt->bindParam(2, $id);
      $stmt->execute();
    } catch (PDOException $e) {
      echo $e->getMessage();
      die(500);
    }
  }

  public function getShort(string $url)
  {
    if (!isset($url)) {
      http_response_code(400);
      echo json_encode([
        "message" => "No Url given"
      ]);
      die;
    }
    $res = $this->checkIfUrlExists($url);
    if (!$res) {
      // Create new Entry
      $short = $this->createNewEntry($url);
      return $short;
    } else {
      return $res;
    }
  }

  public function updateCount(int $id): void
  {
    if (!isset($id) || $id == 0) {
      die(500);
    }
    $this->updateCounter($id);
  }

  public function getAllLinks()
  {
    $con = $this->connect();
    $stmt = $con->prepare("SELECT * FROM shorts");
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num < 0) {
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $rows;
    } else {
      return [];
    }
  }

  public function getOneLink(int $id)
  {
    if (!isset($id) || $id == 0) {
      echo "No ID given";
      die(500);
    }
    $con = $this->connect();
    $stmt = $con->prepare("SELECT * FROM shorts WHERE id=?");
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num < 0) {
      $rows = $stmt->fetch(PDO::FETCH_ASSOC);
      return $rows;
    } else {
      return [];
    }
  }

  public function deleteEntry(int $id): void
  {
    if (!isset($id) || $id == 0) {
      echo "No ID given";
      die(500);
    }
    try {

      $con = $this->connect();
      $stmt = $con->prepare("DELETE FROM shorts WHERE id = ?");
      $stmt->bindParam(1, $id);
      $stmt->execute();
    } catch (PDOException $e) {
      echo $e->getMessage();
      die(500);
    }
  }
}
