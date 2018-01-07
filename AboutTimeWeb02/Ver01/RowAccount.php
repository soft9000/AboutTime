<?php

include_once 'headers.php';

class RowAccount {

    var $ID = -1;
    var $email = 'never';
    var $password = 'never';
    var $weekStart = 2; // Monday
    var $dayWindow = 7;

    function isNull() {
        return ($this->ID === -1);
    }

    function assign($event) {
        if (is_a($event, 'RowAccount') === false) {
            return false;
        }
        $this->ID = $event->ID;
        $this->email = $event->email;
        $this->password = $event->password;
        $this->weekStart = $event->weekStart;
        $this->dayWindow = $event->dayWindow;
        return true;
    }

    function assignFromArray($row) {
        if (isset($row['password']) == false) {
            return false;
        }
        $this->ID = $row['ID'];
        $this->email = $row['email'];
        $this->password = $row['password'];
        $this->weekStart = $row['weekStart'];
        $this->dayWindow = $row['dayWindow'];
        return true;
    }

}

?>