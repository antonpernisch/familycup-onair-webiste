<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-cache");

require_once "../../config.php";
require_once "../../php/db_helper.php";

$out = array();
$db = new DB( DB_SERVER, DB_NAME, DB_USER, DB_PASS );
$out["state"] = $db->GetOption( "live_state" );
$out["next"] = $db->GetOption( "live_nextID" );

echo json_encode( $out );