<?php

function HtmlPrint($message) {
    return ("<center>" . $message . "</center>");
}

function HtmlEcho($message) {
    print("<center>" . $message . "</center>");
}

function HtmlDebug($message) {
    print('<div class=errorprint>' . $message . '</div>');
}

?>
