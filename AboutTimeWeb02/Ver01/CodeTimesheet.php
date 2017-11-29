<?php

include_once 'headers.php';

class CodeTimesheet extends AbsFormProcessor {

    protected function getFormName() {
        return "FormTimesheet";
    }

    function doFormRequest() {
        HtmlEcho($this->getFormName());
    }

}
?>

