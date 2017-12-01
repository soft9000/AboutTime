<?php

include_once 'headers.php';

class CodeEvent extends AbsFormProcessor {

    var $event = NULL;
    var $request = -1;

    function __construct() {
        $this->event = new RowEvent();
    }
    
    function setEvent($doit, $event) {
        $this->event = $event;
    }

    public function getHeader($css) {
        $result = '<!DOCTYPE html>' .
                '<html>' .
                '    <head>' .
                '       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' .
                '       <title>AboutTime - Web Edition</title>' .
                '       <link rel="stylesheet" type="text/css" href="' . $css . '">' .
                '<script>
            function startTime() {
                var today = new Date();
                document.getElementById("localtime_epoch").value = today;
                var h = today.getHours();
                var m = today.getMinutes();
                var s = today.getSeconds();
                h = pad(h);
                m = pad(m);
                s = pad(s);
                document.getElementById("localtime_tick").value =
                h + ":" + m + ":" + s + " (" + createOffset(today) + " UTC)";
                var t = setTimeout(startTime, 500);
            }
            function pad(value) {
                return value < 10 ? "0" + value : value;
            }
            function createOffset(date) {
                var sign = (date.getTimezoneOffset() > 0) ? "-" : "+";
                var offset = Math.abs(date.getTimezoneOffset());
                var hours = pad(Math.floor(offset / 60));
                var minutes = pad(offset % 60);
                return sign + hours + ":" + minutes;
            }
        </script>' .
                '   </head>' .
                '<body onload="startTime()">';
        return $result;
    }

    protected function getFormName() {
        return "FormEvent";
    }

    protected function getFormResponse($request) {
        $zname = $this->getFormName();
        $event = new RowEvent();

        $result = $this->getHomeLink();
        $result .= '<form action="' . $this->getFormFileName() . '" method="post">';
        $result .= '<table>';
        // START MENU BUTTONS
        $result .= "\n <tr><td></td><td>";
        $result .= '<input type="submit" name="' . $zname . '" class="buttonmedium" value="Create">';
        if ($guid != -1) {
            $result .= '    <input type="submit" name="' . $zname . '" class="buttonmedium" value="Update">';
            $result .= '    <input type="submit" name="' . $zname . '" class="buttonmedium" value="Delete">';
            $result .= "\n";
        }
        $result .= "<hr></td></tr>\n";
        // STOP MENU BUTTONS
        $guid = $this->event->eventGUID;
        $result .= '    <tr><td class="field_lbl">Time:</td> <td><input name="ignoretime" id="localtime_tick" class="notebox" value="' . $this->event->localtime . '" readonly></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td class="field_lbl">Stars:</td> <td><input name="stars" class="field_txt" value="' . $this->event->stars . '"></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td class="field_lbl">Subject:</td> <td><input name="subject" class="field_txt" value="' . $this->event->subject . '"></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td class="field_lbl">Entry:</td><td></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td></td><td><textarea name="event" class="notebox" rows="10" cols="40">' . $this->event->message . '</textarea></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td></td><td><input name="localtime" id="localtime_epoch" class="notebox_ro" value="' . $this->event->localtime . '" hidden></td>';
        $result .= "\n";
        $result .= '    <tr><td></td><td><b>Event ID: <input type="text" class="qnum" name="eventGUID" value="' . $guid . '" readonly></b></td></tr>';
        $result .= '</table>';
        $result .= '</form>';
        return $result;
    }

    protected function readFrom_REQUEST() {
        if (isset($_REQUEST[$this->getFormName()]) === false) {
            HtmlDebug("Error 001 - readFrom_REQUEST");
            return false;
        }
        $tmp = $_REQUEST[$this->getFormName()];
        $this->request = trim($tmp);

        if (isset($_REQUEST['eventGUID']) === false) {
            HtmlDebug("Error 101 - readFrom_REQUEST");
            return false;
        }
        $tmp = $_REQUEST['eventGUID'];
        $this->event->eventGUID = trim($tmp);

        if (isset($_REQUEST['subject']) === false) {
            HtmlDebug("Error 201 - readFrom_REQUEST");
            return false;
        }
        $tmp = $_REQUEST['subject'];
        $this->event->subject = trim($tmp);

        if (isset($_REQUEST['event']) === false) {
            HtmlDebug("Error 301 - readFrom_REQUEST");
            return false;
        }
        $tmp = $_REQUEST['event'];
        $this->event->message = $tmp;

        if (isset($_REQUEST['localtime']) === false) {
            HtmlDebug("Error 401 - readFrom_REQUEST");
            return false;
        }
        $tmp = $_REQUEST['localtime'];
        $this->event->localtime = trim($tmp);

        if (isset($_REQUEST['stars']) === false) {
            HtmlDebug("Error 501 - readFrom_REQUEST");
            return false;
        }
        $tmp = $_REQUEST['stars'];
        $this->event->stars = trim($tmp);

        HtmlDebug("Success: readFrom_REQUEST - " . $this->event->localtime);
        return true;
    }

    protected function doFormRequest() {
        $br = false;
        if ($this->readFrom_REQUEST()) {
            $ip = new IpTracker();
            $db = Database::OpenDatabase($ip);
            if ($this->request == 'Update') {
                if ($this->event->eventGUID == -1) {
                    $this->request = 'Create';
                } else {
                    $br = $db->update($this->event);
                }
            }
            if ($this->request == 'Delete') {
                if ($this->event->eventGUID != -1) {
                    $br = $db->delete($this->event);
                } else {
                    $br = true; // gigo
                }
            }
            if ($this->request == 'Create') {
                $br = $db->append($this->event);
            }
            if ($br === false) {
                return HtmlPrint("Event Operation Error!");
            } else {
                ;
            }
        }
        return $this;
    }

}

?>