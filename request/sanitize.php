<?php
namespace limepie\request;
use limepie\request;

class sanitize
{

    private static function getRaw($key)
    {

        if(!request::$data)
        {
            throw new \Exception('\limepie\request::initialize() 가 실행되지 않았습니다.');
        }
        //var $input, $tmp;
        $tmp   = explode("\\", get_called_class());
        $input = end($tmp);

        if(TRUE === isset(request::$data[$input][$key]))
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

        //var $sanitized, $val;
        $val = self::getRaw($key);
        $num = 0;
        $sanitized = filter_var(filter_var($val, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
        if(FALSE === $sanitized && FALSE === request::isEmpty($defvar))
        {
            $sanitized = filter_var(filter_var(request::getValue(NULL, $defvar), FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
            $num=1;
        }
        if(!$sanitized)
        {
            if($num == 1)
            {
                $val = $defvar;
            }
            self::triggerError($val, ++$num);
        }
        return $sanitized;

    }

    public static function url($key, $defvar=NULL)
    {

        //var $sanitized, $val;
        $val = self::getRaw($key);
        $num = 0;
        $sanitized = filter_var(filter_var($val, FILTER_SANITIZE_URL), FILTER_VALIDATE_URL);
        if(FALSE === $sanitized && FALSE === request::isEmpty($defvar))
        {
            $sanitized = filter_var(filter_var(request::getValue(NULL, $defvar), FILTER_SANITIZE_URL), FILTER_VALIDATE_URL);
            $num=1;
        }
        if(!$sanitized)
        {
            if($num == 1)
            {
                $val = $defvar;
            }
            self::triggerError($val, ++$num);
        }
        return $sanitized;

    }

    public static function int($key, $defvar=NULL)
    {

        //var $sanitized, $val;
        $val = self::getRaw($key);
        $num = 0;
        $sanitized = filter_var(filter_var($val, FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
        if(FALSE === $sanitized && FALSE === request::isEmpty($defvar))
        {
            $sanitized = filter_var(filter_var(request::getValue(NULL, $defvar), FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
            $num=1;
        }

        if(FALSE === $sanitized)
        {
            if($num == 1)
            {
                $val = $defvar;
            }
            self::triggerError($val, ++$num);
        }
        return $sanitized;

    }

    public static function float($key, $defvar=NULL)
    {

        //var $sanitized, $val, $option;
        $option = FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_SCIENTIFIC;

        $val = self::getRaw($key);
        $num = 0;
        $sanitized = filter_var(filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, 20480), FILTER_VALIDATE_FLOAT, 20480);
        if(FALSE === $sanitized && FALSE === request::isEmpty($defvar))
        {
            $sanitized = filter_var(filter_var(request::getValue(NULL, $defvar), FILTER_SANITIZE_NUMBER_FLOAT, 20480), FILTER_VALIDATE_FLOAT, $option);
            $num=1;
        }
        if(FALSE === $sanitized)
        {
            if($num == 1)
            {
                $val = $defvar;
            }
            self::triggerError($val, ++$num);
        }
        return $sanitized;

    }

    public static function string($key, $defvar=NULL)
    {

        return request::getValue(filter_var(self::getRaw($key), FILTER_SANITIZE_STRING), $defvar);

    }

    public static function htmlentities($key, $defvar=NULL)
    {

        return request::getValue(filter_var(self::getRaw($key), FILTER_SANITIZE_FULL_SPECIAL_CHARS), $defvar);

    }

    public static function htmlescape($key, $defvar=NULL)
    {

        return request::getValue(filter_var(self::getRaw($key), FILTER_SANITIZE_SPECIAL_CHARS), $defvar);

    }

}