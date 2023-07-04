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
if (isset($data->id)) {
  $id = $data->id;
}

if ($id == null) {
  echo json_encode([
    "message" => "No ID given"
  ]);
  die;
}

$databaseService->deleteEntry($id);
echo json_encode([
  "message" => "Deleted"
]);
