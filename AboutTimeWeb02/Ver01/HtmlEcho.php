<?php

$VERBOSE = FALSE;

function HtmlPrint($message) {
    return ("<center>" . $message . "</center>");
}

function HtmlEcho($message) {
    print("<center>" . $message . "</center>");
}

function HtmlError($message) {
    global $VERBOSE;
    if ($VERBOSE == TRUE) {
        print("<center>" . $message . "</center>");
    }
}

function HtmlDebug($message) {
    HtmlError($message);
}

?>
