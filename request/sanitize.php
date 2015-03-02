<?php
namespace limepie\request;
use limepie\request\validate;
use limepie\request;

class sanitize// extends validate
{

    public static $defaultNoticeError = FALSE;

    public static function run($dataName, $key, $type, $default=NULL)
    {

        $val  = validate::getData($dataName,$key);
        $data = '';

        if(FALSE === request::isEmpty($val))
        {
            switch (trim($type))
            {
                case   'email':
                    $data = self::email($val);
                    break;
                case   'url':
                    $data = self::url($val);
                    break;
                case   'int':
                    $data = self::int($val);
                    break;
                case   'float':
                    $data = self::float($val);
                    break;
                case   'string':
                    $data = self::string($val);
                    break;
                case   'boolean':
                    $data = self::boolean($val);
                    break;
                case   'htmlentities':
                    $data = self::htmlentities($val);
                    break;
                case   'htmlescape':
                    $data = self::htmlescape($val);
                    break;
                case   'raw':
                    $data = self::raw($val);
                    break;
                default :
                    //error
                    $caller = debug_backtrace()[2];
                    trigger_error("Undefined method: ".$type." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_NOTICE);
                    return;
                    break;
            }
        }

        if(TRUE === request::isEmpty($data))
        {
            if (gettype ($default) == "object" && ($default instanceof \Closure))
            {
                $data = $default($val);
            }
            else
            {
                $data = $default;
            }
        }

        $valid = TRUE;
        if(FALSE === request::isEmpty($data))
        {
            switch ($type)
            {
                case   'email':
                    $valid = validate::email($data);
                    break;
                case   'url':
                    $valid = validate::url($data);
                    break;
                case   'int':
                    $valid = validate::int($data);
                    break;
                case   'float':
                    $valid = validate::float($data);
                    break;
                default :
                    $valid = $data;
                    break;
            }
        }

        if(TRUE === is_null($data) || FALSE === request::isEmpty($valid))
        {
            return $data;
        }
        else
        {
            $caller = debug_backtrace()[3];
            if(TRUE === is_null($default))
            {
                trigger_error("Argument 1 passed to ".$type." or NULL must be an instance of ".$caller['function'].", ".gettype($data)." given, called in ".$caller['file'].' on line '.$caller['line'].''.' and defined ', E_USER_NOTICE);
            }
            else
            {
                trigger_error("Argument 3 passed to ".$type." or NULL must be an instance of ".$caller['function'].", ".gettype($data)." given, called in ".$caller['file'].' on line '.$caller['line'].''.' and defined ', E_USER_NOTICE);
            }
        }

    }

    public static function __callStatic($name, $arguments)
    {

        if(TRUE === in_array($name, ['post', 'get', 'session', 'cookie', 'server', 'parameter', 'segment']))
        {
            array_unshift($arguments, $name);
            if(TRUE === self::$defaultNoticeError && 3 === count($arguments))
            {
                $caller = debug_backtrace()[1];
                trigger_error("Missing argument 3 for ".$caller['class'].$caller['type'].$caller['function'].", called in ".$caller['file'].' on line '.$caller['line'].''.' and defined ', E_USER_NOTICE);
            }

            return forward_static_call_array('self::run', $arguments);
        }
        $caller = debug_backtrace()[1];
        trigger_error("Undefined method: ".$name." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_NOTICE);
        return;

    }

    public static function email($val)
    {

        return filter_var(filter_var($val, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);

    }

    public static function url($val)
    {

        return filter_var(filter_var($val, FILTER_SANITIZE_URL), FILTER_VALIDATE_URL);

    }

    public static function int($val)
    {

        return filter_var(filter_var($val, FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);

    }

    public static function float($val)
    {

        return filter_var(filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, 20480), FILTER_VALIDATE_FLOAT, 20480);

    }

    public static function string($val)
    {

        return filter_var($val, FILTER_SANITIZE_STRING);

    }

    public static function htmlentities($val)
    {

        return filter_var($val, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    }

    public static function htmlescape($val)
    {

        return filter_var($val, FILTER_SANITIZE_SPECIAL_CHARS);

    }

    public static function boolean($val)
    {

        $validated = filter_var($val, FILTER_VALIDATE_BOOLEAN, 134217728);
        return TRUE !== is_null($validated);

    }

    public static function raw($val)
    {

        return TRUE !== is_null($val) ? $val : FALSE;

    }

}