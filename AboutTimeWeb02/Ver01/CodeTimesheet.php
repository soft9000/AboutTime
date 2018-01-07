<?php

include_once 'headers.php';

class CodeTimesheet extends AbsFormProcessor {

    function __construct() {
        // parent::__construct();
    }

    protected function getFormName() {
        return "FormTimesheet";
    }

    function doFormRequest() {
        HtmlEcho($this->getFormName());
        return $this;
    }

    protected function getFormResponse($request) {
        $response = '';
        $ip = new IpTracker();
        $db = Database::OpenDatabase($ip);
        if ($this->event_guid == -1) {
            
        } else {
            
        }
        return $response;
    }

    /**
     * Pagination operations relative to the posted information.
     * 
     * @param type $db a PUBLIC database
     * @return boolean False on error / none found, else an array of rows found.
     */
    private function readNextNavSet($db) {
        $total = $db->countAccounts();
        if ($total < 1) {
            return false;
        }
        if ($this->page_size < 1) {
            // sanity
            global $PAGE_MAX;
            $this->page_size = $PAGE_MAX;
        }

        $dirFirst = '';
        $dirLast = '';
        $frame = 0;
        $this->_fixup($total);
        if ($this->direction == 1) {
            $dirFirst = '>=';
            $dirLast = '<=';
            $frame = $this->logical + $this->page_size;
        } else {
            $dirFirst = '<=';
            $dirLast = '>=';
            $frame = abs($this->logical - $this->page_size);
        }
        $this->_fixup($total);

        $cmd = 'SELECT * FROM DBEVENT WHERE ' .
                ' ID ' . $dirFirst . ' ' . $this->logical .
                ' AND T.ID ' . $dirLast . ' ' . $frame .
                ' ) ORDER BY ID LIMIT ' . $this->page_size . ';';
        HtmlDebug($cmd);
        $rows = $db->query($cmd);
        $result = array();
        while ($row = $rows->fetchArray()) {
            $event = new RowEvent();
            if ($event->assignFromArray($row) === true)
                array_push($result, $event);
        }
        return $result;
    }

    /**
     * Sacroscaint: Avoid the temptation  to change / share this outside of the class!
     * @param type $total
     */
    private function _fixup($total) {
        // param-in fixup
        if ($this->logical < 1) {
            // first page
            $this->logical = 1;
        }
        if ($this->logical + $this->page_size > $total) {
            // last page
            $this->logical = abs($total - $this->page_size);
        }
    }

}
?>

