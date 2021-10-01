<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-cache");

require_once "../../config.php";
require_once "../../php/db_helper.php";

$db = new DB( DB_SERVER, DB_NAME, DB_USER, DB_PASS );
$out = array();

mysqli_query( $db->conn, "SET NAMES utf8;" );
$result = mysqli_query( $db->conn, "SELECT * FROM live_recent ORDER BY recordId" );

while( $thisData = mysqli_fetch_assoc( $result ) ) {
    $recordId = $thisData["recordId"];
    $type = $thisData["type"];
    $assocId = $thisData["assocId"];
    $infoDataArr = explode(";", $thisData["infoData"]);
    $startNo = $infoDataArr[0];
    $categ = $infoDataArr[1];
    $startType = $infoDataArr[2];
    $startTime = $infoDataArr[3];
    $thisout = array( "recordId"=>$recordId, "assocId"=>$assocId, "type"=>$type, "startNo"=>$startNo, "categ"=>$categ, "startType"=>$startType, "startTime"=>$startTime );
    array_push( $out, $thisout );
}

echo json_encode( $out );