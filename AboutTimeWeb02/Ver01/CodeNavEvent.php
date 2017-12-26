<?php

include_once 'headers.php';

class CodeNavEvent extends AbsFormProcessor {

    var $event_guid = -1;     // unique 
    var $event_id = -1;       // primary key
    var $admin = "0";   // admin flag
    var $filter = "undefined";
    var $op = '';
    var $debug = false;

    function __construct() {
        global $ZPUBLIC;
        $this->admin = $ZPUBLIC;
    }

    protected function getFormName() {
        return "FormNavEvent";
    }

    function setRedirect($doit, $event) {
        $this->event_guid = $event->eventGUID;
        $this->event_id = $event->ID;
        $this->admin = true; // TODO: implied?
    }

    protected function doFormRequest() {
        $result = $this;
        // Do we have a NAVIGATIONAL FORM request for an EVENT?
        $name = $this->getFormName();
        if (isset($_REQUEST[$name]) == true) {
            $tmp = $_REQUEST[$name];
            HtmlDebug("NAV GBU = [" . $tmp . "]");
            $this->filter = trim($tmp);
            if (isset($_REQUEST["admin"]) == true) {
                $tmp = $_REQUEST["admin"];
                HtmlDebug("NAV ADMIN = " . $tmp);
                $this->admin = $tmp;
            }
            if (isset($_REQUEST["op"]) == true) {
                $this->op = $_REQUEST["op"];
                HtmlDebug("NAV OP = " . $this->op);
            }
            if (isset($_REQUEST["guid"]) == true) {
                $tmp = $_REQUEST["guid"];
                HtmlDebug("ENTRY GUID = " . $tmp);
                $this->event_guid = $tmp;
            }
            if (isset($_REQUEST["dbid"]) == true) {
                $tmp = $_REQUEST["dbid"];
                HtmlDebug("ENTRY DBID = " . $tmp);
                $this->event_id = $tmp;
            }
        } else {
            HtmlDebug("NO NAV!");
        }
        if ($this->op == "EDIT") {
            $ip = new IpTracker();
            $db = Database::OpenDatabase($ip);
            $event = new RowEvent();
            $event->ID = $this->event_id;
            $event->eventGUID = $this->event_guid;
            if ($db->read($event) == false) {
                $this->op = "NEXT"; // Just ignore it?
            } else {
                // Re-direct to CodeEvent editor ...
                $result = new CodeEvent();
                // $result->doFormRequest();
                $result->setRedirect($this, $event);
            }
        }
        return $result;
    }

    protected function getFormResponse($request) {
        $ip = new IpTracker();
        $db = Database::OpenDatabase($ip);
        $event = new RowEvent();
        $event->ID = $this->event_id;
        $event->eventGUID = $this->event_guid;
        if ($db->read($event) == true) {
            switch ($this->op) {
                case "NEXT":
                    if ($db->readNext($event) == false) {
                        return HtmlPrint("No more events.");
                    }
                    break;
                case "PREV":
                    if ($db->readPrev($event) == false) {
                        return HtmlPrint("No more events.");
                    }
                    break;
                case "EDIT":
                    if ($db->read($event) == false) {
                        return HtmlPrint("Unable to read editing event.");
                    }
                    break;
                default:
                    break;
            }
        }

        if ($event->isNull()) {
            if ($this->event_guid == -1) {
                if ($db->read($event) == false) {
                    return HtmlPrint("Unable to locate a first event?");
                }
            } else {
                $event->eventGUID = $this->event_guid;
                if ($db->read($event) == false) {
                    return HtmlPrint("Unable to read event " . $event->eventGUID . ".");
                }
            }
        }
        if ($event->ID === -1 || $event->eventGUID === -1) {
            $event->ID = $this->event_id;
            $event->eventGUID = $this->event_guid;
            if ($db->read($event) == false) {
                return HtmlPrint("Unable to locate an event?");
            }
        }
        return $this->getEventView($event);
    }

    function isNull() {
        return ($this->event_guid === -1);
    }

    function isDebug() {
        return $this->debug;
    }

    function isAdmin() {
        global $ZADMIN;
        return ($this->admin == $ZADMIN);
    }

    function getFormNav($event) {
        $result = $this->getHomeLink();
        $form = $this->getFormFileName();
        $name = $this->getFormName();
        if ($this->isDebug()) {
            $result .= "\n";
        }
        $result .= '<div style="color:0xffffff;">';
        $result .= '<form action = "' . $form . '" id = "formnav" method = "post">';
        $result .= '<input hidden name = "' . $name . '" form = "formnav" >';
        $result .= "\n&nbsp;&nbsp;";
        $result .= '<input type = "submit" class="buttonmedium" name = "op" value = "PREV">';
        $result .= "\n&nbsp;&nbsp;";
        $result .= '<input type = "submit" class="buttonmedium" name = "op" value = "NEXT">';
        $result .= "\n&nbsp;&nbsp;";
        $result .= '<input type = "submit" class="buttonmedium" name = "op" value = "EDIT">';
        $result .= "\n&nbsp;&nbsp;";
        if ($this->isDebug()) {
            $result .= "\n";
        }
        if ($this->isAdmin() == false) {
            // $result .= '<input class="buttonlike" name = "admin" value = "' . $this->admin . '">';
        } else {
            $result .= '<input type = "hidden" name = "admin" value = "' . $this->admin . '">';
        }
        $result .= '<input type = "hidden" name = "guid" value = "' . $event->eventGUID . '">';
        $result .= '<input type = "hidden" name = "dbid" value = "' . $event->ID . '">';
        $result .= '</form>';
        if ($this->isDebug()) {
            $result .= "\n";
        }
        return $result;
    }

    /**
     * Markup RowEvent into an HTML form - complete with navigation.
     * 
     * @param type $event A valid event
     */
    public function getEventView($event) {
        if (is_a($event, 'RowEvent') === false) {
            return HtmlPrint("Form TYPE ERROR!");
        }
        if ($event->ID === -1 || $event->eventGUID === -1) {
            return HtmlPrint("zError: " . print_r($event));
        }
        $result = $this->getFormNav($event);
        $result .= CodeNavEvent::GetEventDisplay($event);
        return $result;
    }

    /**
     * A read-only, tabular display of a RowEvent.
     * 
     * @param type $event
     */
    public static Function GetEventDisplay($entry) {
        if (is_a($entry, 'RowEvent') === false) {
            return HtmPrint("GetEventDisplay: TYPE ERROR!");
        }
        $result = '';
        $result .= "\n<table>";
        $result .= "\n<tr><td class='field_lbl_ro'>Entry Weight: </td><td><input name='stars' class='field_ro' value='$entry->stars' readonly></td></tr>\n";
        $result .= "\n<tr><td class='field_lbl_ro'>Entry Date: </td><td><input name='localtime' class='field_ro' value='$entry->localtime' readonly></td></tr>\n";

        $result .= "\n<tr><td class='field_lbl_ro'>Subject: </td><td><input name='subject' class='field_ro' value='$entry->subject' readonly></td></tr>\n";
        $result .= "\n<tr><td class='field_lbl_ro'>Entry: </td><td></td></tr>\n";
        $result .= "\n<tr><td></td><td><textarea name='message' class='notebox' rows='10' cols='40' readonly>$entry->message</textarea></td></tr>\n";
        $result .= "\n<tr><td></td><td></td></tr>\n";
        $result .= "\n</table>\n";
        return $result;
    }

}
