<?php

include_once "headers.php";

/**
 * The class for PUBLIC quote-review.
 *  The PUBLIC is limited to classifying 'stuff for their own IP address, only.
 */
class DbPublic {

    var $bpublic = true;
    var $db = NULL;

    function __construct() {
        
    }

    public function countEvents() {
        $cmd = 'SELECT count(*) FROM DBEVENT;';
        $rows = $this->db->query($cmd);
        if ($row = $rows->fetchArray()) {
            return $row[0];
        }
        return 0;
    }

    /**
     * FORMS textarea CAN RECEIVE A \r, AS WELL AS \n
     * 
     * @param type $str
     * @return type
     */
    static function Normalize($str) {
        $str = str_replace("\r", '', $str);
        $str = str_replace("\n", '<br>', $str);
        return $str;
    }

    static function DeNormalize($str) {
        $str = str_replace("<br>", "\r\n", $str);

        return $str;
    }

    static function MkGUID() {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    function open($isLocal) {
        if ($isLocal) {
            global $DBLOCAL;
            $this->db = new SQLite3($DBLOCAL);
        } else {
            global $DBFILE;
            $this->db = new SQLite3($DBFILE);
        }
    }

    function isPublic() {
        return $this->bpublic;
    }

    function read($event) {
        if (is_a($event, 'RowEvent') === false) {
            return false;
        }

        if ($this->countEvents() === 0) {
            return false;
        }

        $results = false;
        $guid = $event->eventGUID;
        if ($guid === -1) {
            HtmlDebug("<hr/>read x ID(" . $event->ID . "," . $event->eventGUID . ")<hr/>");
            $results = $this->db->query('SELECT * FROM DBEVENT ORDER BY ID LIMIT 1;');
        } else {
            HtmlDebug("<hr/>read X GUID(" . $event->ID . "," . $event->eventGUID . ")<hr/>");
            $results = $this->db->query('SELECT * FROM DBEVENT WHERE GUID = "' . $guid . '" LIMIT 1;');
        }
        $row = $results->fetchArray();
        if ($row != false) {
            $br = $event->assignFromArray($row);
            $event->message = DbPublic::DeNormalize($event->message);
            return $br;
        }
        return false;
    }

    private function _readNext($event, $op) {
        if (is_a($event, 'RowEvent') === false) {
            return false;
        }

        if ($this->countEvents() === 0) {
            return false;
        }

        $results = false;
        if ($event->ID < 1) {
            return $this->read(-1);
        } else {
            $results = $this->db->query($op);
        }
        $row = $results->fetchArray();
        if ($row != false) {
            $br = $event->assignFromArray($row);
            $event->message = DbPublic::DeNormalize($event->message);
            return $br;
        }
        return false;
    }

    function readNext($event) {
        return $this->_readNext($event, "SELECT * FROM DBEVENT WHERE (epochtime > $event->epochtime) ORDER BY epochtime ASC LIMIT 1;");
    }

    function readPrev($event) {
        return $this->_readNext($event, "SELECT * FROM DBEVENT WHERE (epochtime < $event->epochtime) ORDER BY epochtime DESC LIMIT 1;");
    }

    public function countAccounts() {
        $cmd = 'SELECT count(*) FROM DBUSER;';
        $rows = $this->db->query($cmd);
        if ($row = $rows->fetchArray()) {
            return $row[0];
        }
        return 0;
    }

    /**
     * Will read the first account if isNull(), else will read a 1's based account ID.
     * @param type $account
     * @return boolean
     */
    function readAccount($account) {
        if (is_a($account, 'RowAccount') === false) {
            return false;
        }

        $results = false;

        HtmlDebug("<hr/>read x ID( $account->ID )<hr/>");
        if ($account->ID < 1) {
            $results = $this->db->query('SELECT * FROM DBUSER ORDER BY ID LIMIT 1;');
        } else {
            $results = $this->db->query("SELECT * FROM DBUSER WHERE ID = $account->ID LIMIT 1;");
        }
        $row = $results->fetchArray();
        if ($row != false) {
            $br = $account->assignFromArray($row);
            return $br;
        }
        return false;
    }

    function appendAccount($account) {
        if (is_a($account, 'RowAccount') === false) {
            return false;
        }
        HtmlDebug("<hr/>append x ID( $account->ID )<hr/>");
        $br = false;
        $this->db->enableExceptions(true);
        try {
            $cmd = $this->db->prepare('INSERT INTO DBUSER (ID, email, password, weekStart, dayWindow, pageSize, payload) '
                    . 'VALUES (NULL, :email, :password, :weekStart, :dayWindow, :pageSize, :payload);');
            $cmd->bindParam(':email', $account->email, SQLITE3_TEXT);
            $cmd->bindParam(':password', $account->password, SQLITE3_TEXT);
            $cmd->bindParam(':weekStart', $account->weekStart, SQLITE3_INTEGER);
            $cmd->bindParam(':dayWindow', $account->dayWindow, SQLITE3_INTEGER);
            $cmd->bindParam(':dayWindow', $account->pageSize, SQLITE3_INTEGER);
            $cmd->bindParam(':payload', $account->payload, SQLITE3_TEXT);
            $br = $cmd->execute();
        } catch (Exception $ex) {
            HtmlEcho($ex->getMessage());
        }
        $this->db->enableExceptions(false);
        return $br;
    }


    function updateAccount($account) {
        if (is_a($account, 'RowAccount') === false || $account->ID < 0) {
            HtmlDebug("Db Account Update - TYPE / ROW ID ERROR!");
            return false;
        }
        HtmlDebug("<hr/>update x ID( $account->ID )<hr/>");
        $br = false;
        if ($account->ID > 0) {
            $this->db->enableExceptions(true);
            try {
                $cmd = $this->db->prepare('UPDATE DBUSER SET email=:email, password=:password, weekStart=:weekStart, dayWindow=:dayWindow, pageSize=:pageSize, payload=:payload '
                        . " WHERE ID = '$account->ID';");
                $cmd->bindParam(':email', $account->email, SQLITE3_TEXT);
                $cmd->bindParam(':password', $account->password, SQLITE3_TEXT);
                $cmd->bindParam(':weekStart', $account->weekStart, SQLITE3_INTEGER);
                $cmd->bindParam(':dayWindow', $account->dayWindow, SQLITE3_INTEGER);
                $cmd->bindParam(':pageSize', $account->pageSize, SQLITE3_INTEGER);
                $cmd->bindParam(':payload', $account->payload, SQLITE3_TEXT);
                $br = $cmd->execute();
            } catch (Exception $ex) {
                HtmlEcho($ex->getMessage());
            }
            $this->db->enableExceptions(false);
        } else {
            $br = $this->appendAccount($account);
        }
        return $br;
    }

    /**
     * Will update existing, or append / create new row if isNull()
     * @param type $account
     * @return boolean
     */
    function updateOrAppendAccount($account) {
        if (is_a($account, 'RowAccount') === false) {
            return false;
        }
        if ($account->ID < 1) {
            return $this->appendAccount($account);
        } else {
            return $this->updateAccount($account);
        }
        return false;
    }

}
