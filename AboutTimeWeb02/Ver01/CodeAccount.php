<?php

include_once 'headers.php';

class CodeAccount extends AbsFormProcessor {

    protected function getFormName() {
        return "FormAccount";
    }

    function doFormRequest() {
        HtmlEcho($this->getFormName());
        return $this;
    }

}
?>

