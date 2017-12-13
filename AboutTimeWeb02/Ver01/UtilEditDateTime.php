<?php

/**
 * UNLOVED: Caveat User ... for now!
 * 
 * We need to be able to rationally edit the date and time of an event... ? 
 *
 * @author profnagy
 */
class UtilEditDateTime {

    // In PHP 5, calling non-static methods statically generates an E_STRICT level warning. 
    public static $css_select = 'edit_sel';
    public static $id_year = 'edit_year';
    public static $id_month = 'edit_month';
    public static $id_day = 'edit_day';
    public static $id_hour = 'edit_hour';
    public static $id_minute = 'edit_min';
    public static $id_second = 'edit_sec';

    public static function GetSelect($start, $count, $selected, $id) {
        $result = "<select class='HtmlTimeEdit::$css_select' id='$id'>\n";
        for ($ss = $start; $ss < $count; $ss++) {
            if ($ss == $selected) {
                $result .= " <option value='$ss' selected>$ss</option>";
            } else {
                $result .= " <option value='$ss'>$ss</option>";
            }
        }
        $result .= "\n</select>";
        return $result;
    }

    public static function EditTime($hh, $mm, $ss) {
        $result = GetSelect(1, 24, $hh, UtilEditDateTime::$id_hour);
        $result .= ":";
        $result .= GetSelect(1, 60, $mm, UtilEditDateTime::$id_minute);
        $result .= ":";
        $result .= GetSelect(1, 60, $ss, UtilEditDateTime::$id_second);
        return $result;
    }

    public static function EditDay($dd, $mm, $yy) {
        $result = GetSelect(1, 31, $dd, UtilEditDateTime::$id_day);
        $result .= "-";
        $result .= GetSelect(1, 12, $mm, UtilEditDateTime::$id_month);
        $result .= "-";
        $result .= GetSelect($yy - 3, 7, $yy, UtilEditDateTime::$id_year);
        return $result;
    }

}
