<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-cache");

require_once "../../config.php";
require_once "../../php/db_helper.php";

$include_info = isset($_GET["include_info"]);
$include_state = isset($_GET["include_state"]);

$db = new DB(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
mysqli_query($db->conn, "SET NAMES utf8;");

$out = array();

$query = "SELECT * FROM live_rozpis";
$result = mysqli_query($db->conn, $query);

$nextAssocID = $db->GetOption("live_nextID");
$state = $db->GetOption("live_state");

if($include_state) array_push($out, $state);

while ($thisData = mysqli_fetch_assoc($result)) {
    if ($thisData["assocId"] == "0" && !$include_info) continue;
    $tempData = explode(";", $thisData["infoData"]);
    $time = explode(":", $tempData[3]);
    $startTime = ($time[0] * 60) + $time[1];
    unset($tempData[3]);
    $thisData["infoData"] = $tempData;
    $evaluated = $db->IsEvaluated($thisData["assocId"]);
    $startedTime = $thisData["assocId"] == $nextAssocID ? $db->GetOption("live_startedTime") : false;
    $thisout = array("assocID" => $thisData["assocId"], "data" => $thisData["infoData"], "startTime" => $startTime, "canceled" => $thisData["canceled"], "evaluated" => $evaluated, "started" => $startedTime);
    array_push($out, $thisout);
}

echo json_encode($out);
