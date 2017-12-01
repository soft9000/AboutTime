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

    function readNext($event) {
        if (is_a($event, 'RowEvent') === false) {
            return false;
        }

        if ($this->countEvents() === 0) {
            return false;
        }

        $row = $event->ID;
        $results = false;
        if ($row < 1) {
            return $this->read($event);
        } else {
            $results = $this->db->query('SELECT * FROM DBEVENT WHERE ID > ' . $row . ' LIMIT 1;');
        }
        $row = $results->fetchArray();
        if ($row != false) {
            $br = $event->assignFromArray($row);
            $event->message = DbPublic::DeNormalize($event->message);
            return $br;
        }
        return false;
    }

}
