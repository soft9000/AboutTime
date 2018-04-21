<?php

include_once 'RequestAccount.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RequestEventList
 *
 * @author profnagy
 */
class RequestEventList extends RequestAccount {
    
    // Directions
    const dRefresh = 10;
    const dNext = 20;
    const dFirst = 30;
    const dLast = 40;
    const dPrev = 50;
    
    var $direction = RequestEventList::dRefresh;
    var $top_id = 1;
}
