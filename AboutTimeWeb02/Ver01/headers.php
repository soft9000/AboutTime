<?php

$COPYRIGHT = '<div class="smallprint"><br>About Time System. Version 0.04. Created <b>2017-11-28</b><br></div>';

$WEBROOT = 'http://localhost:8000';
$ZADMIN = 'YourPassword';
$ZPUBLIC = 'PUBLIC';
$DBFILE = '../AtData01/AboutTime2017.sqlt3';
$DBLOCAL = '../AtData01/testing_db.sqlt3';
$BACKUP = '../AtData01/atdata.sql';
$PAGE_MAX = 50; // viewable list per-page maximum.

$VERDIR = "Ver01/";

include_once 'HtmlEcho.php';

include_once 'IpTracker.php';

// Request Headers
include_once 'RequestAccount.php';
include_once 'RequestEventList.php';
include_once 'RequestEventFilter.php';

// Entities
include_once 'Database.php';
include_once 'DbPublic.php';
include_once 'DbUser.php';
include_once 'RowEvent.php';
include_once 'RowAccount.php';

// include_once 'UtilEditDateTime.php';

include_once 'AbsFormProcessor.php';
include_once 'CodeWelcome.php';
include_once 'CodeNavEvent.php';
include_once 'CodeAccount.php';
include_once 'CodeEvent.php';
include_once 'CodeEventList.php';
include_once 'CodeTimesheet.php';

include_once 'CodeAPI.php'; // HTTP Forms-Request API



