<?php

include_once 'headers.php';

class MenuTop extends IpTracker {

    var $direction = 0; // -1 back, = 0 refresh, 1 = forward
    var $qnum = -1;     // quote primary key
    var $admin = "0";   // logical admin flag
    var $logical = 1;   // logical quote number
    var $filter = "undefined";
    var $movement = '';
    var $debug = false;

    function __construct() {
        parent::__construct();
        global $ZPUBLIC;
        $this->admin = $ZPUBLIC;
    }

    function isNull() {
        return ($this->qnum === -1);
    }

    function isDebug() {
        return $this->debug;
    }

    function isAdmin() {
        global $ZADMIN;
        return ($this->admin == $ZADMIN);
    }

    function readFrom_REQUEST() {
        // Do we have a NAV-FORM post?
        if (isset($_REQUEST["GbuNav"]) == true) {
            $tmp = $_REQUEST["GbuNav"];
            HtmlDebug("NAV GBU = [" . $tmp . "]");
            $this->filter = trim($tmp);
            if (isset($_REQUEST["logical"]) == true) {
                $tmp = $_REQUEST["logical"];
                HtmlDebug("NAV LOGICAL = " . $tmp);
                $this->logical = $tmp;
            }
            if (isset($_REQUEST["admin"]) == true) {
                $tmp = $_REQUEST["admin"];
                HtmlDebug("NAV ADMIN = " . $tmp);
                $this->admin = $tmp;
            } if (isset($_REQUEST["qnum"]) == true) {
                $tmp = $_REQUEST["qnum"];
                HtmlDebug("NAV QNUM = " . $tmp);
                $this->qnum = $tmp;
            } if (isset($_REQUEST["movement"]) == true) {
                $this->movement = $_REQUEST["movement"];
                HtmlDebug("NAV MOVEMENT = " . $this->movement);
            }
            return true;
        } else {
            HtmlDebug("NO NAV!");
        }
        return false;
    }

    function procNav() {
        switch ($this->direction === 1) {
            case 1:
                $this->logical += 1;
                return;
            case -1:
                $this->logical -= 1;
                return;
            case 0:
            default:
                return;
        }
    }

    function ShowEventNav($form, $nav, $event) {
        if ($nav->isDebug()) {
            echo "\n";
        }
        echo '<div style="color:0xffffff;">';
        echo '<form action = "' . $form . '" id = "formnav" method = "post">';
        echo '<input hidden name = "GbuNav" form = "formnav" >';
        echo "&nbsp;&nbsp;";
        echo '<input type = "submit" class="buttonmedium" name = "movement" value = "NEXT">';
        echo "&nbsp;&nbsp;";
        echo '<input type = "submit" class="buttonmedium" name = "movement" value = "KEEP">';
        echo "&nbsp;&nbsp;";
        echo '<input type = "submit" class="buttonmedium" name = "movement" value = "OMIT">';
        echo "&nbsp;&nbsp;";
        if ($nav->isDebug()) {
            echo "\n";
        }
        if ($nav->isAdmin() == false) {
            echo '<input class="buttonlike" name = "admin" value = "' . $nav->admin . '">';
        } else {
            echo '<input type = "hidden" name = "admin" value = "' . $nav->admin . '">';
        }
        // TODO: echo '<input type = "submit" name = "movement" value = "PREV">';
        echo '<input type = "hidden" name = "logical" value = "' . $nav->logical . '">';
        echo '<input type = "hidden" name = "qnum" value = "' . $event->eventGUID . '">';
        echo '</form>';
        if ($nav->isDebug()) {
            echo "\n";
        }
        return true;
    }

    function ShowHomeLink() {
        global $WEBROOT;
        echo "\n";
        echo '<table class="logo"><tr><td>';
        echo "\n";
        echo '<img src="http://www.TheQuoteForToday.com/TheQuoteForToday.gif">';
        echo '</td><td class="menu">';
        echo '<a href="' . $WEBROOT . '">[Home]</a>';
        echo "\n";
        echo '</td></tr></table>';
        echo "\n";
    }

    function ShowFormNav($form, $nav, $event) {
        return ShowEventNav($form, $nav, $event);
    }

}
