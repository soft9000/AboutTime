<?php

include_once 'headers.php';

class RowEvent {

    var $ID = -1;
    var $eventGUID = -1;
    var $localtime = -1;
    var $stars = 3;
    var $subject = "undefined";
    var $message = "undefined";

    function isNull() {
        return ($this->eventGUID === -1);
    }

    function assign($event) {
        if (is_a($event, 'RowEvent') === false) {
            return false;
        }
        $this->subject = $event->subject;
        $this->event = $event->event;
        $this->eventGUID = $event->eventGUID;
        HtmlDebug("ASSIGN " . $this->subject . " " . $event->event . "!!!");
        return true;
    }

    function assignFromArray($row) {
        if (isset($row['localtime']) == false) {
            return false;
        }
        $this->ID = $row['ID'];
        $this->eventGUID = $row['guid'];
        $this->localtime = $row['localtime'];
        $this->stars = $row['stars'];
        $this->subject = $row['subject'];
        $this->message = $row['entry'];
        return true;
    }

}

?>