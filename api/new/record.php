<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-cache");

require_once "../../config.php";
require_once "../../php/db_helper.php";

$token = $_GET["auth"];
$recordType = $_GET["recType"];
$categ = $_GET["categ"];
$startNo = $_GET["startNo"];
$startTime = $_GET["startTime"];
$startType = $_GET["startType"];
$dataToWrite = $_GET["data"];
$scored = $_GET["scored"];
$assocId = $_GET["assocID"];
$published = date("H:i");
$out = array();

if (isset($token) && isset($recordType) && isset($categ) && isset($startNo) && isset($startTime) && isset($startType) && isset($dataToWrite) && isset($scored)) {
    if ($token == AUTH_TOKEN) {
        $db = new DB(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
        if ($recordType == "vysledky") {
            $infoData = $startNo . ";" . $categ . ";" . $startType . ";" . $startTime;
            $assocId = $db->FindExact("live_rozpis", "infoData", $infoData)["assocId"];
        }
        if (!isset($assocId)) $assocId = false;
        $foundData = $db->FindPart("live_recent", "infoData", "%;$categ;$startType;%");
        if (($foundData != null || $foundData != false) && $recordType == "rozpis") {
            if ($db->UpdateExistngRecord($recordType, $foundData["assocId"], $categ, $startNo, $startType, $startTime, $dataToWrite, $scored, $published) != false) {
                echo json_encode(array("response" => "success", "info" => "updated"));
            } else {
                echo json_encode(array("response" => "db_error"));
            }
        } else {
            if ($db->AddNewRecord($recordType, $assocId, $categ, $startNo, $startType, $startTime, $dataToWrite, $scored, $published) != false) {
                echo json_encode(array("response" => "success", "info" => "created"));
            } else {
                echo json_encode(array("response" => "db_error"));
            }
        }
    } else {
        echo json_encode(array("response" => "unauth"));
    }
} else {
    echo json_encode(array("response" => "missingargs"));
}
