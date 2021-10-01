<?php
header("Cache-Control: no-cache");

require_once "../../config.php";
require_once "../../php/db_helper.php";

$token = $_GET["auth"];
$newstate = $_GET["nstate"];

$nowTime = date("H:i:s");

$opening = array("inprogress", "paused", "free");

if (isset($token) && isset($newstate)) {
    if ($token == AUTH_TOKEN) {
        $db = new DB(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
        if ($db->SetOption("live_state", $newstate)) {
            in_array($newstate, $opening) ? $db->SetOption("live_running", "true") : $db->SetOption("live_running", "false");
            if ($newstate == "inprogress") $db->SetOption("live_startedTime", $nowTime);
            echo "updated";
        } else {
            echo "failed";
        }
    } else {
        echo "unauthorized";
    }
}
