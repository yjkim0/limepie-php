<?php
namespace limepie\request;
use limepie\request;

class validate
{

    private static function getRaw($key)
    {

        if(!request::$data)
        {
            throw new \Exception('\limepie\request::initialize() 가 실행되지 않았습니다.');
        }
        //var $input, $tmp, $value;
        $tmp   = explode("\\", get_called_class());
        $input = end($tmp);

        if(FALSE === isset(request::$data[$input][$key]))
        {
            return request::$data[$input][$key];
        }
        else
        {
            $caller = debug_backtrace()[1];

            trigger_error("Undefined variable: ".$caller["args"][0]." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_NOTICE);
        }

    }

    private static function triggerError($val, $num=1)
    {

        $caller = debug_backtrace()[1];
        trigger_error("Argument ".$num." passed to ".get_called_class().$caller['type'].$caller['function']."() must be an instance of ".$caller['function'].", ".gettype($val)." given, called in ".$caller['file'].' on line '.$caller['line'].''.' and defined ', E_USER_NOTICE);

    }

    public static function email($key, $defvar=NULL)
    {

        //var $validated, $val, $num;
        $val = self::getRaw($key);
        $num = 0;
        $validated = filter_var($val, FILTER_VALIDATE_EMAIL);
        if(FALSE === $validated && FALSE === request::isEmpty($defvar))
        {
            $validated = filter_var(request::getValue(NULL, $defvar), FILTER_VALIDATE_EMAIL);
            $num=1;
        }
        if(!$validated)
        {
            if($num == 1)
            {
                $val = $defvar;
            }
            self::triggerError($val, ++$num);
        }
        return $validated;

    }

    public static function url($key, $defvar=NULL)
    {

        //var $validated, $val, $num;
        $val = self::getRaw($key);
        $num = 0;
        $validated = filter_var($val, FILTER_VALIDATE_URL);
        if(FALSE === $validated && FALSE === request::isEmpty($defvar))
        {
            $validated = filter_var(request::getValue(NULL, $defvar), FILTER_VALIDATE_URL);
            $num=1;
        }
        if(!$validated)
        {
            if($num == 1)
            {
                $val = $defvar;
            }
            self::triggerError($val, ++$num);
        }
        return $validated;

    }

    public static function int($key, $defvar=NULL)
    {

        //var $validated, $val, $num;
        $val = self::getRaw($key);
        $num = 0;
        $validated = filter_var($val, FILTER_VALIDATE_INT);
        if(FALSE === $validated && FALSE === request::isEmpty($defvar))
        {
            $validated = filter_var(request::getValue(NULL, $defvar), FILTER_VALIDATE_INT);
            $num=1;
        }
        if(FALSE === $validated)
        {
            if($num == 1)
            {
                $val = $defvar;
            }
            self::triggerError($val, ++$num);
        }
        return $validated;

    }

    public static function float($key, $defvar=NULL)
    {

        //var $validated, $val;
        $val = self::getRaw($key);
        $num=0;
        $validated = filter_var($val, FILTER_VALIDATE_FLOAT, 20480);
        if(FALSE === $validated && FALSE === request::isEmpty($defvar))
        {
            $validated = filter_var(request::getValue(NULL, $defvar), FILTER_VALIDATE_FLOAT, 20480);
            $num=1;
        }
        if(FALSE === $validated)
        {
            if($num == 1)
            {
                $val = $defvar;
            }
            self::triggerError($val, ++$num);
        }
        return $validated;

    }

    public static function boolean($key, $defvar=NULL)
    {

        //var $validated, $val, $num;
        $val = self::getRaw($key);
        $num=0;
        $validated = filter_var($val, FILTER_VALIDATE_BOOLEAN, 134217728);
        if(TRUE === is_null($validated) && FALSE === request::isEmpty($defvar))
        {
            $validated = filter_var(request::getValue(NULL, $defvar), FILTER_VALIDATE_BOOLEAN, 134217728);
            $num=1;
        }
        if(TRUE === is_null($validated) )
        {
            if($num == 1)
            {
                $val = $defvar;
            }
            self::triggerError($val, ++$num);
        }
        return $validated;

    }

}