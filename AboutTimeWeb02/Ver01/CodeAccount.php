<?php

include_once 'headers.php';

class CodeAccount extends AbsFormProcessor {

    var $request = '';
    var $account = NULL;

    function __construct() {
        $this->account = new RowAccount();
    }

    protected function getFormName() {
        return "FormAccount";
    }

    protected function readFrom_REQUEST() {

        $tmp = $this->getForm($this->getFormName());
        if ($tmp === false) {
            HtmlDebug("Error 001 - readFrom_REQUEST");
            return false;
        }
        $this->request = trim($tmp);

        $tmp = $this->getForm('accountID');
        if ($tmp === false) {
            HtmlDebug("Error 010 - readFrom_REQUEST");
            return false;
        }
        $this->account->ID = trim($tmp);

        $tmp = $this->getForm('accountEmail');
        if ($tmp === false) {
            HtmlDebug("Error 020 - readFrom_REQUEST");
            return false;
        }
        $this->account->email = trim($tmp);

        $tmp = $this->getForm('accountPass');
        if ($tmp === false) {
            HtmlDebug("Error 030 - readFrom_REQUEST");
            return false;
        }
        $this->account->password = trim($tmp);

        $tmp = $this->getForm('weekStartDay');
        if ($tmp === false) {
            HtmlDebug("Error 040 - readFrom_REQUEST");
            return false;
        }
        $this->account->weekStart = trim($tmp);

        $tmp = $this->getForm('dayReviewWindow');
        if ($tmp === false) {
            HtmlDebug("Error 050 - readFrom_REQUEST");
            return false;
        }
        $this->account->dayWindow = trim($tmp);

        $tmp = $this->getForm('pageSize');
        if ($tmp === false) {
            HtmlDebug("Error 060 - readFrom_REQUEST");
            return false;
        }
        $this->account->pageSize = trim($tmp);
        
        HtmlDebug("Success: readFrom_REQUEST - " . $this->getFormName());
        return true;
    }

    protected function doFormRequest() {
        $br = false;
        if ($this->readFrom_REQUEST() === false) {
            return $this;
        }
        $ip = new IpTracker();
        $db = Database::OpenDatabase($ip);
        if ($this->request == 'Save') {
            $br = $db->updateOrAppendAccount($this->account);
        }
        if ($br === false) {
            return HtmlPrint("Event Operation Error!");
        } else {
            ;
        }

        return $this;
    }

    /**
     * Must return a string / html response - request is always a FormProcessor:
     */
    protected function getFormResponse($request) {
        $zname = $this->getFormName();
        $ip = new IpTracker();
        $db = Database::OpenDatabase($ip);
        if ($db->readAccount($this->account) === False) {
            if ($db->appendAccount($this->account) == false) {
                return HtmlPrint('Error A100: Unable to read the default account');
            }
            if ($db->readAccount($this->account) === False) {
                return HtmlPrint('Error A110: Unable to read the default account');
            }
        }

        $result = $this->getHomeLink();
        $result .= '<form action="' . $this->getFormFileName() . '" method="post">';
        $result .= '<table>';
        // START MENU BUTTONS
        $result .= "\n <tr><td></td><td>";
        $result .= '    <input type="submit" name="' . $zname . '" class="buttonmedium" value="Save">';
        $result .= "\n";
        $result .= "<hr></td></tr>\n";
        // STOP MENU BUTTONS
        $result .= '    <tr><td class="field_lbl_ro">Email:</td> <td><input name="accountEmail" class="field_ro" value="' . $this->account->email . '" readonly></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td class="field_lbl_ro">AT Password:</td> <td><input name="accountPass" class="field_ro" value="' . $this->account->password . '" readonly></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td class="field_lbl_ro">Day View:</td> <td><input name="dayReviewWindow" type="number" min="7" max = "31" class="field_txt" value="' . $this->account->dayWindow . '"></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td class="field_lbl_ro">Page Size:</td> <td><input name="pageSize"  type="number" min="5" max="100" class="field_txt" value="' . $this->account->pageSize . '"></td></tr>';
        $result .= "\n";

        // WEEK-START SELECTION
        $result .= '    <tr><td class="field_lbl_ro">Week Start:</td><td>';
        $days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        $select = '<select name="weekStartDay" class="field_txt">';
        for ($ss = 0; $ss < count($days); $ss++) {
            $select .= '<option value=';
            $select .= $ss + 1;
            if ($this->account->weekStart == $ss + 1) {
                $select .= ' selected>';
            } else {
                $select .= '>';
            }
            $select .= $days[$ss];
            $select .= '</option>';
        }
        $select .= '</select>';
        $result .= $select;
        $result .= '     </td></tr>';
        $result .= "\n";
        
        // ROW IDENTIFICATION
        $result .= '    <tr><td></td><td><b>Account ID: <input type="text" class="qnum" name="accountID" value="' . $this->account->ID . '" readonly></b></td></tr>';
        $result .= "\n";
        $result .= '</table>';
        $result .= '</form>';
        return $result;
    }

}
?>

