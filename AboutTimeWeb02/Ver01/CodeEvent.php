<?php

include_once 'headers.php';

class CodeEvent extends AbsFormProcessor {

    var $event = NULL;
    var $request = -1;
    var $nav_resume = 0;

    function __construct() {
        $this->event = new RowEvent();
    }

    function setRedirect($doit, $event) {
        $this->event = $event;
        $this->nav_resume = 1;
    }

    public function getHeader($css) {
        $result = "<!DOCTYPE html>\n" .
                "<html>\n" .
                '    <head>' .
                '       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' .
                '       <title>AboutTime - Web Edition</title>' .
                '       <link rel="stylesheet" type="text/css" href="' . $css . '">';
        if ($this->nav_resume == 0) {
            $result .= '<script>
            function startTime() {
                var today = new Date();
                var y = today.getFullYear();
                var mo = today.getMonth();
                var d = today.getDate();
                var h = today.getHours();
                var m = today.getMinutes();
                var s = today.getSeconds();
                document.getElementById("localtime_epoch").value = Date.UTC(y, mo, d, h, m, s);
                mo = pad(mo + 1);
                d = pad(d);
                h = pad(h);
                m = pad(m);
                s = pad(s);
                document.getElementById("localtime_tick").value =
                y + "/" + mo + "/" + d + ": " +
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
        } else {
            $result .= "\n</head>\n<body>\n";
        }
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
        $guid = $this->event->eventGUID;
        if ($guid != -1) {
            $result .= '    <input type="submit" name="' . $zname . '" class="buttonmedium" value="Update">';
            $result .= '    <input type="submit" name="' . $zname . '" class="buttonmedium" value="Delete">';
            $result .= "\n";
        }
        $result .= "<hr></td></tr>\n";
        // STOP MENU BUTTONS
        $result .= '    <tr><td class="field_lbl_ro">Time:</td> <td><input name="localtime_tick" id="localtime_tick" class="notebox" value="' . $this->event->localtime . '" readonly></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td class="field_lbl_ro">Stars:</td> <td><input type="number" min="1" max="5" name="stars" class="field_txt" value="' . $this->event->stars . '"></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td class="field_lbl_ro">Subject:</td> <td><input name="subject" class="field_txt" value="' . $this->event->subject . '"></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td class="field_lbl_ro">Entry:</td><td></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td></td><td><textarea name="event" class="notebox" rows="10" cols="40">' . $this->event->message . '</textarea></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td></td><td><b>Event ID: <input type="text" class="qnum" name="eventGUID" value="' . $guid . '" readonly></b></td></tr>';
        $result .= "\n";
        $result .= '    <tr><td></td><td><input name="localtime_epoch" id="localtime_epoch" value="' . $this->event->epochtime . '" hidden></td>';
        $result .= "\n";
        $result .= '    <tr><td></td><td><input name="nav_resume" id="nav_resume" value="' . $this->nav_resume . '" hidden></td>';
        $result .= "\n";
        $result .= '</table>';
        $result .= '</form>';
        return $result;
    }

    protected function readFrom_REQUEST() {
        $tmp = $this->getForm($this->getFormName());
        if ($tmp === false) {
            HtmlDebug("Error 001 - readFrom_REQUEST");
            return false;
        }
        $this->request = trim($tmp);

        $tmp = $this->getForm('eventGUID');
        if ($tmp === false) {
            HtmlDebug("Error 010 - readFrom_REQUEST");
            return false;
        }
        $this->event->eventGUID = trim($tmp);

        $tmp = $this->getForm('subject');
        if ($tmp === false) {
            HtmlDebug("Error 020 - readFrom_REQUEST");
            return false;
        }
        $this->event->subject = trim($tmp);

        $tmp = $this->getForm('event');
        if ($tmp === false) {
            HtmlDebug("Error 030 - readFrom_REQUEST");
            return false;
        }
        $this->event->message = $tmp;

        $tmp = $this->getForm('localtime_tick');
        if ($tmp === false) {
            HtmlDebug("Error 040 - readFrom_REQUEST");
            return false;
        }
        $this->event->localtime = trim($tmp);

        $tmp = $this->getForm('localtime_epoch');
        if ($tmp === false) {
            HtmlDebug("Error 050 - readFrom_REQUEST");
            return false;
        }
        $this->event->epochtime = trim($tmp);
        
        $tmp = $this->getForm('stars');
        if ($tmp === false) {
            HtmlDebug("Error 060 - readFrom_REQUEST");
            return false;
        }
        $this->event->stars = trim($tmp);

        $tmp = $this->getForm('nav_resume');
        if ($tmp === false) {
            HtmlDebug("Error 070 - readFrom_REQUEST");
            return false;
        }
        $this->nav_resume = trim($tmp);

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
        // AFTER EDITING RE-DIRECTION RESUME, if and as required
        if ($this->nav_resume == 1) {
            $response = new CodeNavEvent();
            $response->setRedirect($this, $this->event);
            return $response;
        }
        return $this;
    }

}

?>