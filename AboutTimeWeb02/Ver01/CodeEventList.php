<?php

include_once 'headers.php';

class CodeEventList extends AbsFormProcessor {

    var $req = NULL;
    var $op = -1;
    var $nav_resume = 0;

    function __construct() {
        $this->req = new RequestEventList();
    }

    function setRedirect($doit, $event) {
        $this->req = $event;
        $this->nav_resume = 1;
    }

    public function getHeader($css) {
        $result = "<!DOCTYPE html>\n" .
                "<html>\n" .
                '    <head>' .
                '       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' .
                '       <title>AboutTime - Web Edition</title>' .
                '       <link rel="stylesheet" type="text/css" href="' . $css . '">' .
                "    \n</head>\n" .
                "<body>\n";
        return $result;
    }

    protected function getFormName() {
        return "FormEventList";
    }

    private function getEditButton($zname, $zrow) {
        $result = "<input type='submit' name='$zname' class='buttonmedium' value='Edit ";
        $result .= $zrow['ID'] . "'>";
        return $result;
    }

    protected function getFormResponse($request) {

        if ($this->req->direction == RequestEventList::dEdit) {
            HtmlDebug("Edit $this->op");
            return;
        }
        $zname = $this->getFormName();
        $event = new RowEvent();

        $result = $this->getHomeLink();
        $result .= '<form action="' . $this->getFormFileName() . '" method="post">';
        $result .= '<table>';

        // START MENU BUTTONS
        $result .= "\n <tr><td></td><td>";
        $result .= '<input type="submit" name="' . $zname . '" class="buttonmedium" value="First">';
        $result .= '<input type="submit" name="' . $zname . '" class="buttonmedium" value="Prev">';
        $result .= '<input type="submit" name="' . $zname . '" class="buttonmedium" value="Next">';
        $result .= '<input type="submit" name="' . $zname . '" class="buttonmedium" value="Last">';

        $result .= "<hr></td></tr>\n";
        // STOP MENU BUTTONS
        // START STATE
        $result .= '</table>';
        $result .= "\n";
        $result .= '    <tr><td></td><td><input name="accountID" value="' . $this->req->accountID . '" hidden></td>';
        // START DETAIL FORM
        $ip = new IpTracker();
        $db = Database::OpenDatabase($ip);

        $data = $db->list_events($this->req); // also sets $this->req->top_id

        $result .= "\n";
        $result .= '    <tr><td></td><td><input name="top_id" value="' . $this->req->top_id . '" hidden></td>';

        if ($data != False) {
            $result .= '<table>';
            foreach ($data as $zrow) {
                $str = '<tr><td>' . $this->getEditButton($zname, $zrow) 
                        . '</td><td width="50" class="list_ro">&nbsp;&nbsp;' . $zrow['stars'] 
                        . '</td><td class="list_ro">&nbsp;&nbsp; ' . $zrow['localtime'] . '&nbsp;&nbsp;' 
                        . '</td><td width="300" class="list_ro">&nbsp;&nbsp;' . $zrow['subject'] 
                        . '&nbsp;&nbsp;...'
                        . '</td></tr>';
                $result .= "$str\n";
            }
            $result .= "</table>";
        } else {
            $result .= "False?!";
        }
        // END DETAIL FORM
        $result .= "\n";
        $result .= '</form>';

        return $result;
    }

    protected function readFrom_REQUEST() {
        $tmp = $this->getForm($this->getFormName());
        if ($tmp === false) {
            HtmlDebug("Error 001 - readFrom_REQUEST");
            return false;
        }
        $this->op = trim($tmp);

        // CHECK: PROCESS a selected Edit request?
        $vals = explode(' ', $this->op);
        if ($vals[0] == 'Edit') {
            // Mark state - just in case!
            $this->req->direction = RequestEventList::dEdit;
            $this->op = trim($vals[1]);
            return true;
        }
        // NORMAL MENU SELECTIONS:
        switch ($this->op) {
            case 'First':
                $this->req->direction = RequestEventList::dFirst;
                break;
            case 'Prev':
                $this->req->direction = RequestEventList::dPrev;
                break;
            case 'Next':
                $this->req->direction = RequestEventList::dNext;
                break;
            case 'Last':
                $this->req->direction = RequestEventList::dLast;
                break;
            default:
                $this->req->direction = RequestEventList::dRefresh;
        }

        $tmp = $this->getForm('accountID');
        if ($tmp === false) {
            HtmlDebug("Error 010 - readFrom_REQUEST");
            return false;
        }
        $this->req->accountID = trim($tmp);

        $tmp = $this->getForm('top_id');
        if ($tmp === false) {
            HtmlDebug("Error 020 - readFrom_REQUEST");
            return false;
        }
        $this->req->top_id = trim($tmp);

        HtmlDebug("Success: readFrom_REQUEST - " . $this->req->top_id);
        return true;
    }

    protected function doFormRequest() {
        $br = false;
        if ($this->readFrom_REQUEST()) {
            if ($this->req->direction == RequestEventList::dEdit) {
                // Attempt redirect:
                $ip = new IpTracker();
                $db = Database::OpenDatabase($ip);
                $event = new RowEvent();
                $event->ID = $this->op;
                $event->eventGUID = -1;
                if ($db->read($event) == true) {
                    // Re-direct to CodeEvent editor ...
                    $result = new CodeEvent();
                    $result->setRedirect($this, $event);
                    return $result;
                } else {
                    HtmlDebug("Unable to locate record!");
                }
            }
        }
        return $this;
    }

}

?>