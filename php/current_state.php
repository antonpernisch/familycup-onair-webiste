<?php
require_once "{$PATH_TO_HOME}php/db_helper.php";
require_once "{$PATH_TO_HOME}php/visual_helper.php";
require_once "{$PATH_TO_HOME}config.php";

class CurrentState {
    protected $db;
    protected $visual;

    function __construct( $hpath ) {
        $this->db = new DB(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
        $this->visual = new VisualHelper( $hpath . "blocks" );
    }

    function Get() {
        return $this->db->GetOption("live_state");
    }

    function GetDisplay() {
        return $this->visual->GetBlock( "state/" . $this->Get() );
    }
}