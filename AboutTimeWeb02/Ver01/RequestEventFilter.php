<?php

include_once 'RequestEventList.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RequestEventFilter
 *
 * @author profnagy
 */
class RequestEventFilter extends RequestEventList {
    var $pwEpoch = -1;
    var $filterText = '';
    var $subjectOnly = FALSE;
}
