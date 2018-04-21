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

    protected function getFormResponse($request) {
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
        $result .= "\n";
        $result .= '</form>';
        
        if ($data != False) {
            foreach ($data as $zrow) {
                $str = $zrow['ID'] . ', ' . $zrow['subject'] . ', ' . $zrow['stars'];
                $result .= "$str </br>";
            }
        } else {
            $result .= "False?!";
        }
        // END DETAIL FORM

        $result .= 'bingo';
        return $result;
    }

    protected function readFrom_REQUEST() {
        $tmp = $this->getForm($this->getFormName());
        if ($tmp === false) {
            HtmlDebug("Error 001 - readFrom_REQUEST");
            return false;
        }
        $this->op = trim($tmp);

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
            // Okay
        }
        return $this;
    }

}

?>