<?php

namespace limepie\cache;

class redis
{
    private static $instance;

    private function __construct(){}

    private static function getInstance($conn)
    {
        if (FALSE === isset(self::$instance[$conn]))
        {
            self::$instance[$conn] = new \limepie\cache\redis\connect($conn);
        }

        return self::$instance[$conn];
    }

    public static function driver($driver = 'master')
    {
        return self::getInstance($driver);
    }

    public static function set($driver = 'master', $key, $value, $expire = 3600)
    {
        return self::getInstance($driver)->set($key,$value, $expire);
    }

    public static function get($driver = 'master', $key)
    {
        return self::getInstance($driver)->get($key);
    }

}