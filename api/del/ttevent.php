<?php
header("Cache-Control: no-cache");

require_once "../../config.php";
require_once "../../php/db_helper.php";

$token = $_GET["auth"];
$evt_time = $_GET["time"];

if (isset($token) && isset($evt_time)) {
  if ($token == AUTH_TOKEN) {
    $db = new DB(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
    echo $db->RemoveTTEvent($evt_time);
  } else {
    echo "unauthorized";
  }
}
