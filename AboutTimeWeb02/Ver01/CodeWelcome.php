<?php

include_once 'headers.php';

class CodeWelcome extends AbsFormProcessor {

    protected function getFormName() {
        return "FormWelcome";
    }

    function hasFormData() {
        return true; // FormWelcome ALWAYS has someting to show!
    }

    protected function getFormResponse($request) {
        global $VERDIR;
        $form = $VERDIR . $this->getFormFileName();

        $style = 'class="buttonbig" ';
        $response = '<center>';
        $response .= '<form action="' . $form . '" id="' . $this->getFormName() . '" method="post">';
        $response .= '<table>';
        $response .= '    <tr><td><input type="submit" name="op" value="Account" ' . $style . '> </td></tr>';
        $response .= '    <tr><td><input type="submit" name="op" value="New Entries" ' . $style . '> </td></tr>';
        $response .= '    <tr><td><input type="submit" name="op" value="Old Entries" ' . $style . '> </td></tr>';
        $response .= '    <tr><td><input type="submit" name="op" value="List Entries" ' . $style . '> </td></tr>';
        $response .= '</table>';
        $response .= '</form>';
        $response .= '</center>';
        return $response;
    }

    function doFormRequest() {
        $activity = $this;

        if (isset($_REQUEST["op"]) === true) {

            $op = $_REQUEST["op"];

            switch ($op) {
                case 'Account':
                    //HtmlDebug("Account Management<br>");
                    $activity = new CodeAccount();
                    break;
                case 'New Entries':
                    //HtmlDebug("Add Event<br>");
                    $activity = new CodeEvent();
                    break;
                case 'Old Entries':
                    //HtmlDebug("Reporting<br>");
                    $activity = new CodeNavEvent();
                    break;
                case 'List Entries':
                    //HtmlDebug("Reporting<br>");
                    $activity = new CodeEventList();
                    break;
            }
        }
        return $activity;
    }

}
?>

