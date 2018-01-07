<?php

abstract class AbsFormProcessor {

    /**
     * The name used to identify your form post / get activity...
     */
    abstract protected function getFormName();

    /**
     * Must return a FormProcessor .. even if it is yourself!
     */
    abstract protected function doFormRequest();

    /**
     * Returns the PHP file name only - no path, please!
     * 
     * @return string
     */
    protected function getFormFileName() {
        $name = $this->getFormName() . ".php";
        return $name;
    }

    /**
     * Must return a string / html response - request is always a FormProcessor:
     */
    protected function getFormResponse($request) {
        $zname = $this->getFormName();
        return "<center><font color='red'>$zname</font></center>";
    }

    /**
     * Check to see if a browser request has a DoProcessor!
     * @return type Returns true if has a DoProcessor, else false.
     */
    public function hasFormData() {
        return isset($_REQUEST[$this->getFormName()]);
    }

    /**
     * Check to see if a FORM has a NAMED ELEMENT.
     * 
     * @param type $elementName
     * @return boolean
     */
    public function getForm($elementName) {
        if (isset($_REQUEST[$elementName]) === false) {
            return false;
        }
        return $_REQUEST[$elementName];
    }

    public function getHomeLink() {
        global $WEBROOT;
        $result = "\n";
        $result .= '<table class="logo"><tr><td>';
        $result .= "\n";
        $result .= '<img src="http://www.TheQuoteForToday.com/TheQuoteForToday.gif">';
        $result .= '</td><td class="menu">';
        $result .= '<a href="' . $WEBROOT . '">[Home]</a>';
        $result .= "\n";
        $result .= '</td></tr></table>';
        $result .= "\n";
        return $result;
    }

    public function getHeader($css) {
        $result = '<!DOCTYPE html>' .
                '<html>' .
                '    <head>' .
                '       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' .
                '       <title>AboutTime - Web Edition</title>' .
                '       <link rel="stylesheet" type="text/css" href="' . $css . '">' .
                '   </head>' .
                '<body>';
        return $result;
    }

    public function getFooter() {
        global $COPYRIGHT;
        $result = $COPYRIGHT;

        $result .= '</body>' .
                '</html>';
        return $result;
    }

    /**
     * Common event processing / chaining strategy.
     * @global type $COPYRIGHT
     * @param type $activity
     * @param type $css
     * @return boolean
     */
    public static function MainX($activity, $css = "aboutime.css") {
        /* if (is_subclass_of($event, "AbsFormProcessor") === false) {
          HtmlEcho("No Relation");
          return false;
          } */

        $that = $activity;
        if ($activity->hasFormData()) {
            $tmp = $activity->doFormRequest();

            if ($tmp != false) {
                $activity = $tmp;
            }
        }


        echo $activity->getHeader($css);
        echo $activity->getFormResponse($that);
        echo $activity->getFooter();


        return true;
    }

}
