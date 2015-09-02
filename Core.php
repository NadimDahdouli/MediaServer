<?php

/**
 * Description of Core
 *
 * @author Nadim Dahdouli
 */
class Core {

    public $db;
    private static $instance;

    private function __construct() {
        // Setup database connection
        $dsn = "mysql:host=" . Config::read("db.host") .
                ";dbname=" . Config::read("db.dbname") .
                ";charset=utf8";
        $this->db = new PDO($dsn, Config::read("db.user"), Config::read("db.password"));
    }

    public static function getInstance() {
        // Implement the singleton pattern
        if (!isset(self::$instance)) {
            $obj = __CLASS__;
            self::$instance = new $obj;
        }
        return self::$instance;
    }

}