<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-cache");

require_once "../../config.php";
require_once "../../php/db_helper.php";

$token = $_GET["auth"];
$newID = $_GET["assocID"];

if (isset($token) && isset($newID)) {
    if ($token == AUTH_TOKEN) {
        $db = new DB(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
        if ($db->SetOption("live_nextID", $newID)) {
            $out = array("response" => "success");
        } else {
            $out = array("response" => "failed");
        }
    } else {
        $out = array("response" => "unauthorized");
    }
}

echo json_encode($out);
