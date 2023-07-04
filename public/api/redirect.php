<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

include_once("./Database/DatabaseService.php");

$id = null;
$databaseService = new DatabaseService();

$data = json_decode(file_get_contents("php://input"));
if (isset($data->short)) {
  $short = $data->short;
}

if ($short == null) {
  echo json_encode([
    "message" => "No ID given"
  ]);
  die;
}

$databaseService->updateCount($short);
$shortLink = $databaseService->getOneLink($short);

echo json_encode([
  "message" => "Success",
  "short" => $shortLink,
]);
