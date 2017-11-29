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

    /**
     * FORMS textarea CAB RECEIVE A \r, AS WELL AS \n
     * 
     * @param type $str
     * @return type
     */
    static function Normalize($str) {
        $str = str_replace("\r", '', $str);
        $str = str_replace("\n", '<br>', $str);
        return $str;
    }

    static function MkGUID() {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', 
                mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), 
                mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), 
                mt_rand(0, 65535), mt_rand(0, 65535));
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

    function track($nav, $event) {
        if (is_a($nav, "CodeGbuNav") === false || is_a($event, "RowEvent") === false) {
            return false;
        }

        if ($event->eventGUID < 1) {
            return false;
        }

        if ($nav->movement != "OMIT" && $nav->movement != "KEEP") {
            return false;
        }

        $cmd = 'INSERT INTO DBTRACKER VALUES ( NULL, ' .
                '"' . $nav->ip . '", ' .
                '"' . $nav->time . '", ' .
                $event->eventGUID . ', ' .
                '"' . $nav->movement . '"' .
                ');';
        return $this->db->exec($cmd);
    }

    function _append_pages($event) {
        $cmd = 'SELECT * FROM DBPAGE WHERE QUOTE_ID = ' . $event->eventGUID . ';';
        $results = $this->db->query($cmd);
        $event->Quote = $event->Quote . '<hr>Pages:<br/>';
        if ($results == false) {
            return;
        }
        while ($row = $results->fetchArray()) {
            $event->Quote = $event->Quote . '&nbsp;&nbsp;&nbsp;---' . $row['Page'] . '<br/>';
        }
    }

    function read($pkey, $event) {
        HtmlDebug("<hr/>read(" . $pkey . "," . $event->eventGUID . ")<hr/>");
        if (is_a($event, "RowEvent") === false) {
            return false; // LEGACY
        }
        if ($pkey < 0) {
            return $this->readRandom($pkey, $event);
        }
        $qs = new QuoteStatus();
        $results = $this->db->query('SELECT * FROM DBEVENT WHERE ID = ' . $pkey . ' LIMIT 1;');
        $row = $results->fetchArray();
        if ($row != false) {
            $event->eventGUID = $row["ID"];
            $event->subject = $row["Quote"];
            $event->message = $qs->Encode($row["QuoteStatus"]);
            $this->_append_pages($event);
            return true; // LEGACY
        }
        return false; // LEGACY
    }

    function countTrackedChanges() {
        $cmd = 'SELECT count(*) FROM DBTRACKER;';
        $rows = $this->db->query($cmd);
        if ($row = $rows->fetchArray()) {
            return $row[0];
        }
        return 0;
    }

    function countPages($page) {
        $cmd = 'SELECT count(*) FROM DBPAGE WHERE PAGE LIKE("%' . $page . '%");';
        $rows = $this->db->query($cmd);
        if ($row = $rows->fetchArray()) {
            return $row[0];
        }
        return 0;
    }

    /**
     * Sacroscaint: Avoid the temptation  to change / share this outside of the class!
     * @param type $nav
     * @param type $total
     */
    private function _fixup($nav, $total) {
        // param-in fixup
        if ($nav->logical < 1) {
            // first page
            $nav->logical = 1;
        }
        if ($nav->logical + $nav->page_size > $total) {
            // last page
            $nav->logical = abs($total - $nav->page_size);
        }
    }

    /**
     * Pagination operations relative to the $nav information.
     * 
     * @param type $nav GbuNavHistory
     * @return boolean False on error, else the array of rows found.
     */
    function readNextNavSet($nav) {
        if (is_a($nav, "CodeHistoryNav") === false) {
            return false;
        }
        $total = $this->countTrackedChanges();
        if ($total == 0) {
            return false;
        }
        if ($nav->page_size < 1) {
            // sanity
            $nav->page_size = 25;
        }

        $dirFirst = '';
        $dirLast = '';
        $frame = 0;
        $this->_fixup($nav, $total);
        if ($nav->direction == 1) {
            $dirFirst = '>=';
            $dirLast = '<=';
            $frame = $nav->logical + $nav->page_size;
        } else {
            $dirFirst = '<=';
            $dirLast = '>=';
            $frame = abs($nav->logical - $nav->page_size);
        }
        $this->_fixup($nav, $total);

        $cmd = 'SELECT * FROM DBTRACKER AS T JOIN DBEVENT AS Q WHERE (T.QUOTE_ID = Q.ID' .
                ' AND T.ID ' . $dirFirst . ' ' . $nav->logical .
                ' AND T.ID ' . $dirLast . ' ' . $frame .
                ' ) ORDER BY T.ID LIMIT ' . $nav->page_size . ';';
        HtmlDebug($cmd);
        $rows = $this->db->query($cmd);
        $result = array();
        while ($row = $rows->fetchArray()) {
            array_push($result, $row);
        }
        return $result;
    }

    function readQuote($event) {
        if ($this->read($event->eventGUID, $event) === false)
            return false; // LEGACY
        return $event; // LEGACY
    }

}
