<?php

include_once 'headers.php';

class DbUser extends DbPublic {

    function __construct() {
        parent::__construct();
        $this->bpublic = false;
    }

    function append($event) {
        if (is_a($event, 'RowEvent') === false) {
            return false;
        }
        $br = false;
        $event->eventGUID = DbPublic::MkGUID();
        $this->db->enableExceptions(true);
        try {
            $cmd = $this->db->prepare('INSERT INTO DBEVENT (ID, guid, localtime, epochtime, stars, subject, entry) '
                    . 'VALUES (NULL, :guid, :localtime, :epochtime, :stars, :subject, :message);');
            $cmd->bindParam(':guid', $event->eventGUID, SQLITE3_TEXT);
            $cmd->bindParam(':localtime', $event->localtime, SQLITE3_TEXT);
            $cmd->bindParam(':epochtime', $event->epochtime, SQLITE3_INTEGER);
            $cmd->bindParam(':stars', $event->stars, SQLITE3_INTEGER);
            $cmd->bindParam(':subject', $event->subject, SQLITE3_TEXT);
            $dum = DbPublic::Normalize($event->message);
            $cmd->bindParam(':message', $dum, SQLITE3_TEXT);
            $br = $cmd->execute();
        } catch (Exception $ex) {
            HtmlEcho($ex->getMessage());
        }
        $this->db->enableExceptions(false);
        return $br;
    }

    function update($event) {
        if (is_a($event, 'RowEvent') === false || $event->eventGUID < 0) {
            HtmlDebug("Db Update - TYPE / NUMBER ERROR!");
            return false;
        }
        $br = false;
        if ($event->eventGUID != -1) {
            $this->db->enableExceptions(true);
            try {
                $cmd = $this->db->prepare('UPDATE DBEVENT SET localtime= :localtime, stars=:stars, subject=:subject, entry=:message '
                        . " WHERE guid = '$event->eventGUID';");
                $cmd->bindParam(':localtime', $event->localtime, SQLITE3_TEXT);
                $cmd->bindParam(':stars', $event->stars, SQLITE3_INTEGER);
                $cmd->bindParam(':subject', $event->subject, SQLITE3_TEXT);
                $dum = DbPublic::Normalize($event->message);
                $cmd->bindParam(':message', $dum, SQLITE3_TEXT);
                $br = $cmd->execute();
            } catch (Exception $ex) {
                HtmlEcho($ex->getMessage());
            }
            $this->db->enableExceptions(false);
        } else {
            $br = $this->append($event);
        }
        return $br;
    }

    function delete($event) {
        if (is_a($event, 'RowEvent') == false || $event->eventGUID < 0) {
            return true; // gigo
        }
        $cmd = "DELETE FROM DBEVENT WHERE guid = '$event->eventGUID';";
        $br = $this->db->exec($cmd);
        if ($br === true) {
            $event->eventGUID = -1;
            $event->ID = -1;
        }
    }

}

?>