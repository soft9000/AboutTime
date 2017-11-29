<?php

$COPYRIGHT = '<div class="smallprint"><br>About Time System. Version 0.01. Created <b>2017-11-28</b><br></div>';

$WEBROOT = 'http://YourSite.com/AboutTimeWeb02';
$ZADMIN = 'mypassword';
$ZPUBLIC = 'PUBLIC';
$DBFILE = '../AtData01/AboutTime2017.sqlt3';
$DBLOCAL = '../AtData01/testing_db.sqlt3';
$BACKUP = '../AtData01/atdata.sql';

$VERDIR = "Ver01/";

include_once 'HtmlEcho.php';

include_once 'IpTracker.php';
include_once 'MenuTop.php';

include_once 'Database.php';
include_once 'DbPublic.php';
include_once 'DbUser.php';
include_once 'RowEvent.php';

include_once 'AbsFormProcessor.php';
include_once 'CodeWelcome.php';
include_once 'CodeAccount.php';
include_once 'CodeEvent.php';
include_once 'CodeTimesheet.php';



