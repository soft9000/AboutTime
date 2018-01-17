<?php

function HtmlPrint($message) {
    return ("<center>" . $message . "</center>");
}

function HtmlEcho($message) {
    print("<center>" . $message . "</center>");
}

function HtmlError($message) {
    print("<center>" . $message . "</center>");
}

function HtmlDebug($message) {
    HtmlError($message);
}

?>
