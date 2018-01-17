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
    const dRefrest = 10;
    const dNext = 20;
    const dPrev = 30;
    
    var $direction = RequestEventList::dRefrest;
    var $top_id = 1;
}
