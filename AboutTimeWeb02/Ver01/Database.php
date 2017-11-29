<?php

include_once "headers.php";

/**
 * Description of QuoteDatabase
 *
 * @author profnagy
 */
class Database {

    public static function StatDatabase() {
        $ztime = 'Not Found';
        global $DBFILE;
        global $DBLOCAL;
        $nav = new IpTracker();
        if ($nav->isLocal() == false) {
            if (file_exists($DBFILE)) {
                $ztime = filemtime($DBFILE);
                return "<b>Database Last Updated: </b>" . date("F d, Y @ H:i:s.", $ztime);
            }
        } else {
            if (file_exists($DBLOCAL)) {
                $ztime = filemtime($DBLOCAL);
                return "<b>Local Database Last Updated: </b>" . date("F d, Y @ H:i:s.", $ztime);
            }
        }
        return "<b>Database Last Updated: </b>" . $ztime;
    }

    public static function OpenDatabase($ip) {
        /*
          if ($ip->isAdmin()) {
          return Database::OpenUserDatabase();
          } else {
          return Database::OpenPublicDatabase();
          }

         */
        return Database::OpenUserDatabase();
    }

    public static function OpenTestDatabase() {
        $db = new DbUser();
        $db->open(true);
        return $db;
    }

    public static function OpenPublicDatabase() {
        $db = new DbPublic();
        $nav = new IpTracker();
        $db->open($nav->isLocal());
        return $db;
    }

    public static function OpenUserDatabase() {
        $db = new DbUser();
        $nav = new IpTracker();
        $db->open($nav->isLocal());
        return $db;
    }

}
