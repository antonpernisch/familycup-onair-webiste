<?php
header("Cache-Control: no-cache");

require_once "../../config.php";
require_once "../../php/db_helper.php";

$token = $_GET["auth"];
$evt_name = $_GET["name"];
$evt_time = $_GET["time"];

if (isset($token) && isset($evt_name) && isset($evt_time)) {
  if ($token == AUTH_TOKEN) {
    $db = new DB(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
    if ($db->AddNewTTEvent($evt_name, $evt_time, date("H:i"))) {
      echo "success";
    } else {
      echo "failed";
    }
  } else {
    echo "unauthorized";
  }
}
