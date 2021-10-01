<?php
header("Content-Type: text/html;charset=UTF-8");

require_once "{$PATH_TO_HOME}php/db_helper.php";
require_once "{$PATH_TO_HOME}php/visual_helper.php";
require_once "{$PATH_TO_HOME}config.php";

class RecentLoader {
    protected $db;
    protected $visual;

    function __construct( $hpath ) {
        $this->db = new DB(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
        $this->visual = new VisualHelper( $hpath . "blocks" );
        $this->hpath = $hpath;
    }

    function LoadJS( $pageNum ) {
        $out = "REPLACE__THERE";
        $offset = $pageNum == 1 ? 0 : ( ( $pageNum - 1 ) * 6 );

        mysqli_query( $this->db->conn, "SET NAMES utf8;" );
        $query = "SELECT * FROM ( SELECT * FROM live_recent ORDER BY recordId DESC LIMIT 6 OFFSET $offset )Var1 ORDER BY recordId ASC";
        $result = mysqli_query( $this->db->conn, $query );

        while( $thisData = mysqli_fetch_assoc( $result ) ) {
            if( $thisData["type"] === "info" ) continue;
            $recordId = $thisData["recordId"];
            $assocId = $thisData["assocId"];
            $type = $thisData["type"];
            $infoDataArr = explode(";", $thisData["infoData"]);
            $startNo = $infoDataArr[0];
            $categ = $infoDataArr[1];
            $startType = $infoDataArr[2];
            $startTime = $infoDataArr[3];
            $thisout = "
            CardWorker.Generate('$type', '$categ', '$startType', '$startNo', '$startTime', '$recordId', '$assocId',  (out) => {
                $('#to-be-removed').first().remove();
                $('#main-content').prepend(out);
                REPLACE__THERE
              }, preloading=()=>{
                $('#main-content').prepend(\"<div class='text-center my-5' id='to-be-removed'><div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Načítavam...</span></div></div>\");
              }, undefined, '{$this->hpath}blocks/cards/');
            ";
            $out = str_replace( "REPLACE__THERE", $thisout, $out );
        }

        $out = str_replace( "REPLACE__THERE", "", $out );

        return $out == "" ? null : $out;
    }
}