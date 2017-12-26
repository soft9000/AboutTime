<?php

include_once 'headers.php';
include_once "Api.php";

class CodeAPI extends AbsFormProcessor {

    var $request = -1;
    var $payload = -1;
    var $api = -1;

    function __construct() {
        
    }

    public function getHeader($css) {
        $result = "<!DOCTYPE html>\n" .
                "<html>\n" .
                '    <head>' .
                '       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' .
                '       <title>AboutTime - Web Edition</title>' .
                '       <link rel="stylesheet" type="text/css" href="' . $css . '">';
        $result .= "\n</head>\n<body>\n";
        return $result;
    }

    protected function getFormName() {
        return "FormAPI";
    }

    /**
     * This API is an HTML Form. The protocol is a simple POST button-request 
     * containing a "verb" (opcode) with a simple string-payload as a textarea.
     * 
     * For the full rant, see: 
     *    http://soft9000.com/blog9000/index.php?entry=entry150830-022409
     * 
     * @param type $request
     * @return type
     */
    protected function getFormResponse($request) {
        $result = '';
        if ($this->api == -1) {
            $result = $this->getHomeLink();
            $result .= '<form action="' . $this->getFormFileName() . '" method="post">';
            $result .= '<table>';
            $result .= "\n <tr><td></td><td>";
            $result .= '<input type="submit" name="' . $this->getFormName() . '" class="buttonmedium" value="ApiListByDate">';
            $result .= "\n";
            $result .= '    <tr><td></td><td><textarea name="payload" class="notebox" rows="10" cols="40">' . $this->payload . '</textarea></td></tr>';
            $result .= '</table>';
            $result .= '</form>';
        } else {
            $result = $this->api->doApiRequest($this);
        }
        return $result;
    }

    protected function readFrom_REQUEST() {
        if (isset($_REQUEST[$this->getFormName()]) === false) {
            HtmlDebug("Error 001 - readFrom_REQUEST");
            return false;
        }
        $tmp = $_REQUEST[$this->getFormName()];
        $this->request = trim($tmp);

        if (isset($_REQUEST['payload']) === false) {
            HtmlDebug("Error 301 - readFrom_REQUEST");
            return false;
        }
        $tmp = $_REQUEST['payload'];
        $this->payload = $tmp;

        HtmlDebug("Success: readFrom_REQUEST - " . $this->event->localtime);
        return true;
    }

    protected function doFormRequest() {
        $br = false;
        if ($this->readFrom_REQUEST()) {
            switch ($this->request) {
                case "ApiListByDate":
                    $this->api = new ApiListByDate();
                    break;
                case "ApiReadOne":
                    $this->api = new ApiReadOne();
                    break;
                case "ApiUpdateOne":
                    $this->api = new ApiUpdateOne();
                    break;
                case "ApiDeleteOne":
                    $this->api = new ApiDeleteOne();
                    break;
                case "ApiCreateOne":
                    $this->api = new ApiCreateOne();
                    break;
                default:
                    $this->api = new ApiError();
                    break;
            }
        }
        return $this;
    }

}

?>