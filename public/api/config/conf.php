<?
require_once("./config.php");

if (
  !isset($database_host) || $database_host == "" || strlen($database_host) < 1 ||
  !isset($database_user) || $database_user == "" || strlen($database_user) < 1 ||
  !isset($database_password) || $database_password == "" || strlen($database_password) < 1 ||
  !isset($database) || $database == "" || strlen($database) < 1 ||
  !isset($charset) || $charset == "" || strlen($charset) < 1
) {
  http_response_code(500);
  echo json_encode([
    "error" => "Please configure your config.php"
  ]);
  die;
}

define("DB_HOST", $database_host);
define("DB_NAME", $database);
define("DB_USER", $database_user);
define("DB_PASSWORD", $database_password);
define("CHARSET", $charset);
