<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-cache");

require_once "../../config.php";
require_once "../../php/db_helper.php";

$token = $_GET["auth"];
$categ = $_GET["categ"];
$startType = $_GET["sType"];

if (isset($token) && isset($categ) && isset($startType)) {
  if ($token == AUTH_TOKEN) {
    $db = new DB(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
    $foundData = $db->FindPart("live_rozpis", "infoData", "%;$categ;$startType;%");
    if ($foundData != null || $foundData != false) {
      if ($db->CancelRide($foundData["assocId"])) {
        $out = array("response" => "success");
      } else {
        $out = array("response" => "failed");
      }
    } else {
      $out = array("response" => "notFound");
    }
  } else {
    $out = array("response" => "unauthorized");
  }
}

echo json_encode($out);
