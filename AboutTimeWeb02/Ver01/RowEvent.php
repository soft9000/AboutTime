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
        if (is_a($event, "RowEvent") === false) {
            return false;
        }
        $this->subject = $event->subject;
        $this->event = $event->event;
        $this->eventGUID = $event->eventGUID;
        HtmlDebug("ASSIGN " . $this->subject . " " . $event->event . "!!!");
        return true;
    }

    function toArray() {
        $result = array();
        $result[0] = $this->subject;
        $result[1] = $this->message;
        return $result;
    }

    function fromArray($array) {
        if (count($array) != 2) {
            return false;
        }
        $this->subject = $array[0];
        $this->message = $array[1];
        return true;
    }
}


?>