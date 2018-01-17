<?php

include_once 'headers.php';

class RowEvent {

    var $ID = -1;
    var $eventGUID = -1;
    var $uid = 1; // default user id
    var $localtime = -1;
    var $epochtime = -1;
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
        $this->ID = $event->ID;
        $this->eventGUID = $event->eventGUID;
        $this->uid = $event->uid;
        $this->localtime = $event->localtime;
        $this->epochtime = $event->epochtime;
        $this->stars = $event->stars;
        $this->subject = $event->subject;
        $this->message = $event->message;
        return true;
    }

    function assignFromArray($row) {
        if (isset($row['localtime']) == false) {
            return false;
        }
        $this->ID = $row['ID'];
        $this->eventGUID = $row['guid'];
        $this->uid = $row['uid'];
        $this->localtime = $row['localtime'];
        $this->epochtime = $row['epochtime'];
        $this->stars = $row['stars'];
        $this->subject = $row['subject'];
        $this->message = $row['entry'];
        return true;
    }

}

?>