<?php

/**
 * This API 'uses' an HTML Form. The protocol is a simple POST button-request 
 * containing a "verb" (opcode) with a simple string-payload as a textarea.
 * 
 * For the full rant, see: 
 *    http://soft9000.com/blog9000/index.php?entry=entry150830-022409
 * 
 * @param type $request
 * @return type
 */

abstract class AbsApi {

    /**
     * Accept a CodeApi - Return a string response
     */
    abstract protected function doApiRequest($codeApi);
}

include_once "ApiError.php";
include_once "ApiCreateOne.php";
include_once "ApiReadOne.php";
include_once "ApiUpdateOne.php";
include_once "ApiDeleteOne.php";
include_once "ApiListByDate.php";

