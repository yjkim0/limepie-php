<?php

namespace limepie\db;

class mongodb
{
    public static $instance;

    public static function getInstance($conn)
    {

        if (FALSE === isset(self::$instance[$conn]))
        {
            self::$instance[$conn] = new \limepie\db\mongodb\connect($conn);
        }

        return self::$instance[$conn];
    }

    public static function driver($driver = 'master')
    {

        return self::getInstance($driver);

    }

    // public static function gets($driver = 'master', $sql, $bind = [])
    // {

    //     return self::getInstance($driver)->gets($sql, $bind);

    // }

    // public static function get($driver = 'master', $sql, $bind = [])
    // {

    //     return self::getInstance($driver)->get($sql, $bind);

    // }

    // public static function set($driver = 'master', $sql, $bind = [])
    // {

    //     return self::getInstance($driver)->set($sql, $bind);

    // }

    public static function setid($driver = 'master', $db, $collection, $document)
    {

        return self::getInstance($driver)->setid($db, $collection, $document);

    }

    // public static function get1($driver = 'master', $sql, $bind = [])
    // {

    //     return self::getInstance($driver)->get1($sql, $bind);

    // }
}