<?php
namespace limepie\request;
use limepie\request;

class validate extends request
{

    public static $defaultNoticeError = FALSE;

    public static function run($dataName, $key, $type, $default=NULL)
    {

        $val  = validate::getData($dataName,$key);
        $arrType = explode('.', $type);
        if(2 === count($arrType))
        {
            $data = [];
            if(TRUE === is_array($val))
            {
                foreach($val as $key => $value)
                {
                    if($tmp = $this->_run($value, $arrType[1]))
                    {
                        $data[$key] = $tmp;
                    }
                }
            }
            if(TRUE === request::isEmpty($data))
            {
                return $default;
            }
            return $data;
        }
        else
        {
            return self::_run($val, $type, $default);
        }

    }

    private static function _run($val, $type, $default=NULL)
    {

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
                case   'boolean':
                    $data = self::boolean($val);
                    break;
                case   'raw':
                    $data = self::raw($val);
                    break;
                default :
                    //error
                    $caller = debug_backtrace()[3];
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
        $return = '';
        if(FALSE === request::isEmpty($data))
        {
            switch ($type)
            {
                case   'email':
                    $return = self::email($data);
                    break;
                case   'url':
                    $return = self::url($data);
                    break;
                case   'int':
                    $return = self::int($data);
                    break;
                case   'float':
                    $return = self::float($data);
                    break;
                case   'boolean':
                    $return = self::boolean($data);
                    break;
            }
        }
        return $return;

    }

    public static function __callStatic($name, $arguments)
    {

        if(TRUE === in_array($name, ['request', 'post', 'get', 'session', 'cookie', 'server', 'parameter', 'segment']))
        {
            array_unshift($arguments, $name);
            if(TRUE === self::$defaultNoticeError && 3 === count($arguments))
            {
                $caller = debug_backtrace()[1];
                trigger_error("Missing argument 3 for ".$caller['class'].$caller['type'].$caller['function'].", called in ".$caller['file'].' on line '.$caller['line'].''.' and defined ', E_USER_NOTICE);
            }
            if(3 > count($arguments))
            {
                $caller = debug_backtrace()[1];
                trigger_error("Missing argument 3 for ".$caller['class'].$caller['type'].$caller['function'].", called in ".$caller['file'].' on line '.$caller['line'].''.' and defined ', E_USER_NOTICE);
                return;
            }
            return forward_static_call_array('self::run', $arguments);
        }
        $caller = debug_backtrace()[1];
        trigger_error("Undefined method: ".$name." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_NOTICE);
        return;

    }

    public static function email($value)
    {

        return filter_var($value, FILTER_VALIDATE_EMAIL);

    }

    public static function url($value)
    {

        return filter_var($value, FILTER_VALIDATE_URL);

    }

    public static function int($value)
    {

        return filter_var($value, FILTER_VALIDATE_INT);

    }

    public static function float($value)
    {

        return filter_var($value, FILTER_VALIDATE_FLOAT, 20480);

    }

    public static function boolean($value)
    {

        $validated = filter_var($value, FILTER_VALIDATE_BOOLEAN, 134217728);
        return TRUE !== is_null($validated);

    }

}