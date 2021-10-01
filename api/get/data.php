<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-cache");

require_once "../../config.php";
require_once "../../php/db_helper.php";

$db = new DB(DB_SERVER, DB_NAME, DB_USER, DB_PASS);

$recordId = $_GET["id"];
$table = $_GET["table"];
$assocID = $_GET["assocID"];
$noEmpty = isset($_GET["noEmpty"]);

mysqli_query($db->conn, "SET NAMES utf8;");

if (isset($_GET["startCateg"]) && isset($_GET["startType"])) {
    $startCateg = $_GET["startCateg"];
    $startType = $_GET["startType"];

    $thisData = $db->FindPart("live_rozpis", "infoData", "%;$startCateg;$startType;%");
    if ($thisData !== null || $thisData !== false) {
        $type = $thisData["type"] == null ? $table : $thisData["type"];
        $infoDataArr = explode(";", $thisData["infoData"]);
        $startNo = $infoDataArr[0];
        $categ = $infoDataArr[1];
        $startType = $infoDataArr[2];
        $startTime = $infoDataArr[3];
        $publishTime = $thisData["publishedTime"];
        $scored = $thisData["scored"];
        $assocId = $thisData["assocId"];
        $recordId = $thisData["recordId"];
    }
} else {
    if ($assocID != "null") {
        $query = "SELECT * FROM live_$table WHERE assocId=$assocID ORDER BY recordId DESC";
    } else {
        $query = "SELECT * FROM live_$table WHERE recordId=$recordId";
    }
    $result = mysqli_query($db->conn, $query);

    $thisData = mysqli_fetch_assoc($result);

    $type = $thisData["type"] == null ? $table : $thisData["type"];
    $infoDataArr = explode(";", $thisData["infoData"]);
    $startNo = $infoDataArr[0];
    $categ = $infoDataArr[1];
    $startType = $infoDataArr[2];
    $startTime = $infoDataArr[3];
    $publishTime = $thisData["publishedTime"];
    $scored = $thisData["scored"];
    $assocId = $thisData["assocId"];
    $recordId = $thisData["recordId"];
}

$subDataArr = array();

$out = array("assocID" => $assocId, "recordId" => $recordId, "type" => $type, "categ" => $categ, "startNo" => $startNo, "startType" => $startType, "startTime" => $startTime, "published" => $publishTime, "scored" => $scored);
$dataArr = explode("--", $thisData["data"]);
foreach ($dataArr as $currentDataArr) {
    if (empty($currentDataArr)) continue;
    $currentData = explode(";", $currentDataArr);
    $cisloPos = $currentData[0];
    $posadka = $currentData[1];
    if ($noEmpty && $posadka == "(prázdne miesto)") continue;
    $cas = $currentData[2] == "" ? null : $currentData[2];
    $thisout = array("num" => $cisloPos, "posadka" => $posadka, "time" => $cas);
    array_push($subDataArr, $thisout);
}

array_push($out, $subDataArr);
echo json_encode($out);
