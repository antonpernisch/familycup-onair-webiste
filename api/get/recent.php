<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-cache");

require_once "../../config.php";
require_once "../../php/db_helper.php";

$page = $_GET["page"];
$want = $_GET["want"];

$db = new DB( DB_SERVER, DB_NAME, DB_USER, DB_PASS );
$out = array();

if( $page == "all" ) {
    $query = "SELECT * FROM live_recent ORDER BY recordId ASC";
} else {
    $offset = $page == 1 ? 0 : ( ( $page - 1 ) * 6 );
    $query = "SELECT * FROM ( SELECT * FROM live_recent ORDER BY recordId DESC LIMIT 6 OFFSET $offset )Var1 ORDER BY recordId ASC";
}

mysqli_query( $db->conn, "SET NAMES utf8;" );
$result = mysqli_query( $db->conn, $query );

while( $thisData = mysqli_fetch_assoc( $result ) ) {
    $assocId = $want == "record" ? $thisData["recordId"] : $thisData["assocId"];
    if( $thisData["type"] !== "info" ) array_push( $out, $assocId );
}

echo json_encode( $out );