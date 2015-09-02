<?php

/**
 * Description of Config
 *
 * @author Nadim Dahdouli
 */
class Config {

    private static $config;

    public static function read($key) {
        return self::$config[$key];
    }

    public static function write($key, $value) {
        self::$config[$key] = $value;
    }

}
