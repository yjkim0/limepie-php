<?php

namespace limepie;

class model
{

    public static $driver = NULL;
    public static $instance;

    public static function getInstance($conn)
    {

        if (FALSE === isset(self::$instance[$conn]))
        {
            self::$instance[$conn] = new \limepie\model\database($conn);
            self::$instance[$conn]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$instance[$conn]->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            self::$instance[$conn]->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);
        }

        return self::$instance[$conn];

    }

    public static function driver($driver = 'master')
    {

        return self::getInstance($driver);

    }

    public static function db($driver = 'master')
    {

        return self::getInstance($driver);

    }

    public static function begin($driver = 'master')
    {

        return self::getInstance($driver)->begin();

    }

    public static function commit($driver = 'master')
    {

        return self::getInstance($driver)->commit();

    }

    public static function rollback($driver = 'master')
    {

        return self::getInstance($driver)->rollback();

    }

    public static function gets($driver = 'master', $sql, $bind = [])
    {

        return self::getInstance($driver)->gets($sql, $bind);

    }

    public static function get($driver = 'master', $sql, $bind = [])
    {

        return self::getInstance($driver)->get($sql, $bind);

    }

    public static function set($driver = 'master', $sql, $bind = [])
    {

        return self::getInstance($driver)->set($sql, $bind);

    }

    public static function setid($driver = 'master', $sql, $bind = [])
    {

        return self::getInstance($driver)->setid($sql, $bind);

    }

    public static function get1($driver = 'master', $sql, $bind = [])
    {

        return self::getInstance($driver)->get1($sql, $bind);

    }

}
