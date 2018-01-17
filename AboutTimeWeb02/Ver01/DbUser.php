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
            $cmd = $this->db->prepare('INSERT INTO DBEVENT (ID, guid, uid, localtime, epochtime, stars, subject, entry) '
                    . 'VALUES (NULL, :guid, :uid, :localtime, :epochtime, :stars, :subject, :message);');
            $cmd->bindParam(':guid', $event->eventGUID, SQLITE3_TEXT);
            $cmd->bindParam(':uid', $event->uid, SQLITE3_INTEGER);
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

    private function get_page($account, $top_id, $page_size, $dirFirst) {
        $cmd = "SELECT * FROM DBEVENT WHERE (UID = $account->ID AND ID $dirFirst $top_id) ORDER BY ID ";
        if ($dirFirst[0] == '<') {
            $cmd .= ' DESC ';
        }
        $cmd .= " LIMIT $page_size ;";

        HtmlDebug($cmd);
        $rows = $this->db->query($cmd);
        $result = array();
        if ($rows != false) {
            while ($row = $rows->fetchArray()) {
                // HtmlDebug(print_r($row));
                array_push($result, $row);
            }
        }
        return $result;
    }

    /**
     * Pagination operations relative to RequestEventList information.
     * 
     * @param type $nav RequestEventList
     * @return boolean False on error, else the array of rows found.
     */
    private function readNextPage($nav) {
        $account = new RowAccount();
        $account->ID = $nav->accountID;
        if ($this->readAccount($account) == False) {
            HtmlError("Error: readAccount($nav->accountID)!");
            return False;
        }

        $nav->page_size = $account->pageSize;

        if ($nav->page_size < 1) {
            // sanity
            $nav->page_size = RequestAccount::SZPAGE_DEFAULT;
        }

        switch ($nav->direction) {
            case RequestEventList::dNext:
                $data = $this->get_page($account, $nav->top_id, $nav->page_size, '>=');
                if ($data != False && count($data) != 0) {
                    $which = count($data) - 1;
                    $nav->top_id = $data[$which]['ID'];
                    HtmlDebug("dNext " . count($data) . " " . $nav->top_id);
                }
                break;
            case RequestEventList::dPrev:
                $data = $this->get_page($account, $nav->top_id, $nav->page_size, '<=');
                if ($data != False && count($data) != 0) {
                    $which = count($data) - 1;
                    $nav->top_id = $data[$which]['ID'];
                    HtmlDebug("dPrev " . count($data) . " " . $nav->top_id);
                }
                break;
            default:
                break;
        }

        $cmd = "SELECT * FROM DBEVENT WHERE (UID = $account->ID AND ID >= $nav->top_id) ORDER BY ID LIMIT $nav->page_size ;";
        HtmlDebug($cmd);
        $rows = $this->db->query($cmd);
        if ($rows != False) {
            $result = array();
            while ($row = $rows->fetchArray()) {
                array_push($result, $row);
            }
            return $result;
        }
        return False;
    }

    function list_events($nav) {
        if (is_a($nav, 'RequestEventList') == false || $nav->accountID < 1) {
            HtmlDebug("Error list_events(!)");
            return false;
        }

        if ($nav->top_id < 1) {
            $nav->top_id = 1;
        }

        return $this->readNextPage($nav);
    }

}

?>