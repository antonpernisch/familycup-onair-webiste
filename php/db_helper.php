<?php
class DB
{
    public $conn;

    function __construct($server, $name, $user, $pass)
    {
        $this->conn = mysqli_connect($server, $user, $pass, $name);
        if (!$this->conn) {
            die("FATAL: Nepodarilo sa pripojiť na databázu. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
        return true;
    }

    function GetOption($name)
    {
        if (mysqli_ping($this->conn)) {
            $result = mysqli_query($this->conn, "SELECT * FROM nastavenia WHERE nazov='$name' LIMIT 1");
            try {
                return mysqli_fetch_array($result)["hodnota"];
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    function SetOption($name, $newvalue)
    {
        if (mysqli_ping($this->conn)) {
            $result = mysqli_query($this->conn, "UPDATE nastavenia SET hodnota='$newvalue' WHERE nazov='$name' LIMIT 1");
            try {
                if ($result) {
                    return true;
                } else {
                    echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />" . mysqli_error($this->conn);
                    return false;
                }
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }


    function GetNewAssocID()
    {
        if (mysqli_ping($this->conn)) {
            $result = mysqli_query($this->conn, "SELECT MAX( assocId ) AS max FROM live_assoc_lookup");
            try {
                $newAssocId = mysqli_fetch_array($result)["max"] + 1;
                if ($this->AddAssocID($newAssocId)) {
                    return $newAssocId;
                } else {
                    echo "WARN: Pri načítavaní dát sa vyskytol problém";
                    return false;
                }
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    function AddNewRecord($recordType, $assocId, $categ, $startNo, $startType, $startTime, $dataToWrite, $scored, $published)
    {
        if (mysqli_ping($this->conn)) {
            $infoData = implode(";", array($startNo, $categ, $startType, $startTime));
            if ($assocId === false) {
                $assocId = $this->GetNewAssocID();
            } else {
                if ($recordType == "rozpis" && !$this->AssocIDExists($assocId)) $this->AddAssocID($assocId);
            }
            $query = "INSERT INTO live_recent ( assocId, type, infoData, data, publishedTime, scored ) VALUES ( $assocId, '$recordType', '$infoData', '$dataToWrite', '$published', '$scored' );";
            $query = $query . "INSERT INTO live_$recordType ( assocId, infoData, data, publishedTime, scored ) VALUES (  $assocId, '$infoData', '$dataToWrite', '$published', '$scored' );";
            mysqli_query($this->conn, "SET NAMES utf8;");
            $result = mysqli_multi_query($this->conn, $query);
            try {
                if ($result) {
                    return true;
                } else {
                    echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />" . mysqli_error($this->conn);
                    return false;
                }
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    function UpdateExistngRecord($recordType, $assocId, $categ, $startNo, $startType, $startTime, $dataToWrite, $scored, $published)
    {
        if (mysqli_ping($this->conn)) {
            $infoData = implode(";", array($startNo, $categ, $startType, $startTime));
            $query = "UPDATE live_recent SET infoData='$infoData', data='$dataToWrite', publishedTime='$published', scored='$scored' WHERE assocId=$assocId;";
            $query = $query . "UPDATE live_$recordType SET infoData=\"$infoData\", data=\"$dataToWrite\", publishedTime=\"$published\", scored=\"$scored\" WHERE assocId=$assocId;";
            mysqli_query($this->conn, "SET NAMES utf8;");
            $result = mysqli_multi_query($this->conn, $query);
            try {
                if ($result) {
                    return true;
                } else {
                    echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />" . mysqli_error($this->conn);
                    return false;
                }
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    function CancelRide($assocId)
    {
        if (mysqli_ping($this->conn)) {
            $query = "UPDATE live_rozpis SET canceled=1 WHERE assocId=$assocId;";
            mysqli_query($this->conn, "SET NAMES utf8;");
            $result = mysqli_multi_query($this->conn, $query);
            try {
                if ($result) {
                    return true;
                } else {
                    echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />" . mysqli_error($this->conn);
                    return false;
                }
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    function FindExact($table, $col, $data)
    {
        if (mysqli_ping($this->conn)) {
            mysqli_query($this->conn, "SET NAMES utf8;");
            $result = mysqli_query($this->conn, "SELECT * FROM $table WHERE $col='$data'");
            try {
                return mysqli_fetch_array($result);
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    function FindPartWithSel($table, $colToSearch, $selectorCol, $partData, $selectorData)
    {
        if (mysqli_ping($this->conn)) {
            mysqli_query($this->conn, "SET NAMES utf8;");
            $result = mysqli_query($this->conn, "SELECT * FROM $table WHERE $colToSearch LIKE '" . $partData . "' AND $selectorCol=$selectorData ORDER BY recordId LIMIT 1");
            try {
                return mysqli_fetch_array($result);
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    function FindPart($table, $colToSearch, $partData)
    {
        if (mysqli_ping($this->conn)) {
            mysqli_query($this->conn, "SET NAMES utf8;");
            $result = mysqli_query($this->conn, "SELECT * FROM $table WHERE $colToSearch LIKE '" . $partData . "' ORDER BY recordId DESC LIMIT 1");
            try {
                return mysqli_fetch_array($result);
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    function IsEvaluated($assocID)
    {
        if (mysqli_ping($this->conn)) {
            mysqli_query($this->conn, "SET NAMES utf8;");
            //$data = $this->GetInfoFromAssoc("rozpis", $assocID);
            $result = mysqli_query($this->conn, "SELECT * FROM live_vysledky WHERE assocId=$assocID");
            try {
                return mysqli_num_rows($result) > 0;
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    function AddNewTTEvent($evt_name, $evt_time, $published)
    {
        if (mysqli_ping($this->conn)) {
            if ($this->FindPartWithSel("live_rozpis", "infoData", "assocId", "%$evt_time%", "0") !== null) return false;
            $assocId = 0;
            $query = "INSERT INTO live_rozpis ( assocId, infoData, data, publishedTime, scored ) VALUES (  $assocId, ';$evt_name;;$evt_time', '', '$published', 'false' );";
            mysqli_query($this->conn, "SET NAMES utf8;");
            $result = mysqli_multi_query($this->conn, $query);
            try {
                if ($result) {
                    return true;
                } else {
                    echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />" . mysqli_error($this->conn);
                    return false;
                }
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    function RemoveTTEvent($evt_time)
    {
        if (mysqli_ping($this->conn)) {
            $selected = $this->FindPartWithSel("live_rozpis", "infoData", "assocId", "%$evt_time%", "0");
            if ($selected !== false) {
                if ($selected !== null) {
                    $query = "DELETE FROM live_rozpis WHERE recordId=" . $selected["recordId"];
                    mysqli_query($this->conn, "SET NAMES utf8;");
                    $result = mysqli_multi_query($this->conn, $query);
                    try {
                        if ($result) {
                            return "success";
                        } else {
                            echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />" . mysqli_error($this->conn);
                            return false;
                        }
                    } catch (Exception $e) {
                        echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                        return false;
                    }
                } else {
                    return "notFound";
                }
            } else {
                return "error";
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    private function GetInfoFromAssoc($tableFromPull, $assocID)
    {
        if (mysqli_ping($this->conn)) {
            mysqli_query($this->conn, "SET NAMES utf8;");
            $result = mysqli_query($this->conn, "SELECT * FROM live_$tableFromPull WHERE assocId=$assocID LIMIT 1");
            try {
                return mysqli_fetch_array($result);
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    private function AddAssocID($assocIdToAdd)
    {
        if (mysqli_ping($this->conn)) {
            $result = mysqli_query($this->conn, "INSERT INTO live_assoc_lookup ( assocId ) VALUES ( $assocIdToAdd )");
            try {
                if ($result) {
                    return true;
                } else {
                    echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />" . mysqli_error($this->conn);
                    return false;
                }
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    private function AssocIDExists($assocId)
    {
        if (mysqli_ping($this->conn)) {
            $result = mysqli_query($this->conn, "SELECT * FROM live_assoc_lookup WHERE assocId=$assocId LIMIT 1");
            try {
                return mysqli_num_rows($result) > 0;
            } catch (Exception $e) {
                echo "WARN: Pri načítavaní dát sa vyskytol problém:<br />$e";
                return false;
            }
        } else {
            die("FATAL: Spojenie s databázou bolo stratené. Kontaktuje administrátora na t.č. 0918 829 445");
            return false;
        }
    }

    function __destruct()
    {
        try {
            mysqli_close($this->conn);
            return true;
        } catch (Exception $e) {
            return $e;
        }
    }
}
