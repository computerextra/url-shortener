<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

include_once("./Database/DatabaseService.php");

$databaseService = new DatabaseService();
$shorts = $databaseService->getAllLinks();
echo json_encode($shorts);
