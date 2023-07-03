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

  public function postUrl(string $url): void
  {
    if (!isset($url)) {
      http_response_code(400);
      echo json_encode([
        "message" => "no Url Given"
      ]);
      die;
    }
  }
}
