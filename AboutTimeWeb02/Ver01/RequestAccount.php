<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RequestAccount
 *
 * @author profnagy
 */
class RequestAccount {
    const SZPAGE_DEFAULT = 20;
    
    var $accountID = 1; // default account
    
    var $page_size = RequestAccount::SZPAGE_DEFAULT;
}
