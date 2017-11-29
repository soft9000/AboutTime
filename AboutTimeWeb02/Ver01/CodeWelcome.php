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
        $form = $VERDIR . $this->getFormName() . ".php";

        $style = 'class="buttonbig" ';
        $response = '<center>';
        $response .= '<form action="' . $form . '" id="' . $this->getFormName() . '" method="post">';
        $response .=  '<table>';
        $response .=  '    <tr><td><input type="submit" name="op" value="1. Account" ' . $style . '> </td></tr>';
        $response .=  '    <tr><td><input type="submit" name="op" value="2. Add Events" ' . $style . '> </td></tr>';
        $response .=  '    <tr><td><input type="submit" name="op" value="3. Reporting" ' . $style . '> </td></tr>';
        $response .=  '</table>';
        $response .=  '</form>';
        $response .=  '</center>';
        return $response;
    }

    function doFormRequest() {
        $activity = $this;

        if (isset($_REQUEST["op"]) === true) {

            $op = $_REQUEST["op"];

            switch ($op[0]) {
                case '1':
                    //HtmlDebug("Account Management<br>");
                    $activity = new CodeAccount();
                    break;
                case '2':
                    //HtmlDebug("Add Event<br>");
                    $activity = new CodeEvent();
                    break;
                case '3':
                    //HtmlDebug("Reporting<br>");
                    $activity = new CodeTimesheet();
                    break;
            }
        }
        return $activity;
    }

}
?>

