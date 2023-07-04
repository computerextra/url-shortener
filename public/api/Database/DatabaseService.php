<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/config/conf.php");

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
    try {

      $con = $this->connect();
      $stmt = $con->prepare("SELECT * FROM shorts WHERE origin=?");
      $stmt->bindParam(1, $url);
      $stmt->execute();
      $num = $stmt->rowCount();
      if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
      } else {
        return false;
      }
    } catch (PDOException $e) {
      echo "ERROR in 'checkIfUrlExists'" . $e->getMessage();
      die;
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
      $stmt->execute();
      return [
        "message" => "Success",
        "short" => $short
      ];
    } catch (PDOException $ex) {
      echo "ERROR in 'createNewEntry'" . $ex->getMessage();
      die;
    }
  }

  private function getCounter(string $short)
  {
    try {
      $con = $this->connect();
      $stmt = $con->prepare("SELECT count FROM shorts WHERE short=?");
      $stmt->bindParam(1, $short);
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

  private function updateCounter(string $short)
  {
    $count = $this->getCounter($short);
    $count += 1;
    try {
      $con = $this->connect();
      $stmt = $con->prepare("UPDATE shorts SET count=? WHERE short=?");
      $stmt->bindParam(1, $count);
      $stmt->bindParam(2, $short);
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
      return $this->createNewEntry($url);
    } else {
      return $res;
    }
  }

  public function updateCount(string $short): void
  {
    if (!isset($short) || strlen($short) <= 0) {
      die(500);
    }
    $this->updateCounter($short);
  }

  public function getAllLinks()
  {
    $con = $this->connect();
    $stmt = $con->prepare("SELECT * FROM shorts");
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num > 0) {
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $rows;
    } else {
      return [];
    }
  }

  public function getOneLink(string $short)
  {
    if (!isset($short) || strlen($short) < 1) {
      echo "No ID given";
      die(500);
    }
    $con = $this->connect();
    $stmt = $con->prepare("SELECT * FROM shorts WHERE short=?");
    $stmt->bindParam(1, $short);
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num > 0) {
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
