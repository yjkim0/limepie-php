<?php
namespace limepie;

class request
{

    private static $data;

    public static function initialize(\Closure $callback=NULL)
    {

        self::addData('get',     isset($_GET)     ? $_GET     : []);
        self::addData('post',    isset($_POST)    ? $_POST    : []);
        self::addData('cookie',  isset($_COOKIE)  ? $_COOKIE  : []);
        self::addData('session', isset($_SESSION) ? $_SESSION : []);
        self::addData('server',  isset($_SERVER)  ? $_SERVER  : []);

        if($callback)
        {
            return $callback();
        }

    }

    public static function addData($key, $value)
    {

        self::$data[$key] = $value;

    }

    public static function isEmpty($val)
    {

        if(
            TRUE === is_bool($val)
            || TRUE === is_array($val)
            || TRUE === is_object($val)
            || 0 < strlen($val)
            || FALSE === empty($val)
            || FALSE === is_null($val)
        )
        {
            return FALSE;
        }
        return TRUE;

    }

    public static function unsafest($key, \closure $definition)
    {

        $value = self::getRaw($key, FALSE);
        return self::getValue($value, $definition);

    }

    public static function unsafe($key, $definition = NULL)
    {

        $value = self::getRaw($key);
        return self::getValue($value, $definition);

    }

    public static function unsafeAll()
    {

        if(!self::$data)
        {
            throw new \Exception('\limepie\request::initialize() 가 실행되지 않았습니다.');
        }
        //var $input, $tmp;
        $tmp   = explode("\\", get_called_class());
        $input = end($tmp);

        if(TRUE === isset(self::$data[$input]))
        {
            return self::$data[$input];
        }
        else
        {
            $caller = debug_backtrace()[0];
            trigger_error("Undefined type: parameter, argument, segment, get, post, cookie in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_NOTICE);
        }

    }

    public static function getRaw($key, $isNotice = TRUE)
    {
        if(!self::$data)
        {
            throw new \Exception('\limepie\request::initialize() 가 실행되지 않았습니다.');
        }
        //var $input, $tmp;
        $tmp   = explode("\\", get_called_class());
        $input = end($tmp);

        if(TRUE === isset(self::$data[$input][$key]))
        {
            return self::$data[$input][$key];
        }
        else
        {
            if(FALSE === $isNotice)
            {
                return NULL;
            }
            else
            {
                $caller = debug_backtrace()[1];
                trigger_error("Undefined variable: ".$caller["args"][0]." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_NOTICE);
            }
        }

    }

    public static function getValue($value=NULL, $definition=NULL)
    {

        // var $ret = NULL:
        if (gettype ($definition) == "object" && ($definition instanceof \Closure))
        {
            $ret = $definition($value);
        }
        elseif (FALSE === self::isEmpty($value))
        {
            $ret = $value;
        }
        else
        {
            $ret = $definition;
        }

        return $ret;

    }

    public static function defined($key)
    {

        if(!self::$data)
        {
            throw new \Exception('\limepie\request::initialize() 가 실행되지 않았습니다.');
        }
        //var $input, $tmp, $value;
        $tmp   = explode("\\", get_called_class());
        $input = end($tmp);

        if(FALSE === isset(request::$data[$input]))
        {
            $caller = debug_backtrace()[0];
            trigger_error("Undefined type: parameter, argument, segment, get, post, cookie in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_NOTICE);
        }
        else if(TRUE === isset(request::$data[$input][$key]))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }

    }

    public static function isPost()
    {

        return request\server::unsafest('REQUEST_METHOD', function($val) {
            return strtolower($val) == 'post';
        });

    }

    public static function isGet()
    {

        return request\server::unsafest('REQUEST_METHOD', function($val) {
            return strtolower($val) == 'get';
        });

    }

    public static function hasFiles()
    {



    }

    public static function isAjax()
    {

        return request\server::unsafest('HTTP_X_REQUESTED_WITH', function($val) {
            return strtolower($val) == 'xmlhttprequest';
        });

    }

    public static function currentUrl()
    {

        return (strtolower(getenv('HTTPS')) == 'on' ? 'https' : 'http')
            .'://'
            .getenv('HTTP_HOST')
            .(($p = getenv('SERVER_PORT')) != 80 AND $p != 443 ? ":$p" : '')
            .parse_url(getenv('REQUEST_URI'), PHP_URL_PATH)
            .(getenv('QUERY_STRING') ? '?'.getenv('QUERY_STRING') : '')
            ;

    }

}