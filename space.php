<?php


namespace limepie;

/**
 * 싱글톤 패턴, 매직함수를 사용하여 키,값의 저장소로 사용
 *
 * @package       system\space
 * @category      system
 */

class space
{

    private $_variables;
    private static  $_instance;
    public static $name = "globals";

    public function __construct()
    {
        //let self::name = "globals";
    }

    public function __get($key)
    {

        if(TRUE === isset($this->_variables[$key]))
        {
            return $this->_variables[$key];
        } else {
            return [];
        }

    }

    public function __isset($key)
    {

        return isset($this->_variables[$key]);

    }

    public function __set($key, $val)
    {

        $this->_variables[$key] = $val;
        return $val;

    }

    public function __unset($key)
    {

        if(TRUE === isset($this->_variables[$key]))
        {
            unset($this->_variables[$key]);
        }

    }

    /* destroy a variable */
    public function __destruct()
    {

        $this->_variables = NULL;

    }

    public static function getInstance()
    {

        if (!self::$_instance)
        {
            self::$_instance = new self();
        }
        return self::$_instance;

    }

    public static function name($name)
    {

        self::$name = $name;
        return self::getInstance();

    }

    public static function setAttr($arg = [], $val = NULL)
    {

        return self::setAttribute($arg, $val);

    }

    public static function getAttr($attr = NULL, $key = NULL)
    {

        return self::getAttribute($attr, $key);

    }

    public static function getAttrs($name = NULL)
    {

        return self::getAttributes($name);

    }

    public static function setAttribute($arg = [], $val = NULL)
    {

        $name = self::$name;
        $data = self::getInstance()->{$name};
        $instance = self::getInstance();
        if (TRUE === is_array($arg))
        {
            $instance->{$name} = array_merge ($data , $arg);
            return $arg;
        }
        else
        {
            $p = func_get_args();
            if(count($p)>1)
            {
                $instance->{$name} = array_merge ($data, [$arg => $val]);
                return $val;
            }
        }

    }

    public static function getAttributes($name=NULL)
    {

        if(!$name)
        {
            $name = self::$name;
        }
        return self::getInstance()->{$name};

    }

    public static function getAttribute($attr = NULL, $key = NULL)
    {

        $name = self::$name;
        if(TRUE === is_null($attr))
        {
            return self::getInstance()->{$name};
        }
        else
        {
            if(TRUE === isset(self::getInstance()->{$name}[$attr]))
            {
                if($key)
                {
                    if(TRUE === isset(self::getInstance()->{$name}[$attr][$key]))
                    {
                        return self::getInstance()->{$name}[$attr][$key];
                    }
                }
                else
                {
                    return self::getInstance()->{$name}[$attr];
                }
            }
        }
        return NULL;

    }

}
