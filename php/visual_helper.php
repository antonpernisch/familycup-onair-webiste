<?php
class VisualHelper {
    protected $homePath;

    function __construct( $hpath ) {
        $this->homePath = $hpath;
        return true;
    }

    function GetBlock( $path ) {
        try {
            return file_get_contents( $this->homePath . "/" . $path . ".html" );
        } catch( Exception $e ) {
            return "WARN: Nepodarilo sa načítať vizuálny blok<br />[$e]";
        }
    }

    function Info( $title, $content ) {
        try {
            $out = $this->GetBlock( "alerts/info" );
            $out = str_replace( "obsah", $content, str_replace( "nadpis", $title, $out ) );
            return $out;
        } catch( Exception $e ) {
            return "WARN: Nepodarilo sa načítať vizuálny blok<br />[$e]";
        }
    }

    function HideID( $id ) {
        echo "<script>Visual.HideID( '$id' );</script>";
        return true;
    }

    function ChangeContent( $id, $newcontent ) {
        echo "<script>Visual.ChangeContent( '$id', '$newcontent' );</script>";
        return true;
    }
}