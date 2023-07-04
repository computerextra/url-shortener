<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

include_once("./Database/DatabaseService.php");

$url = null;
$databaseService = new DatabaseService();

$data = json_decode(file_get_contents("php://input"));
if (isset($data->url)) {
  $url = $data->url;
}

if ($url == null) {
  echo json_encode([
    "message" => "No Url given"
  ]);
  die;
}

$short = $databaseService->getShort($url);
echo json_encode($short);
