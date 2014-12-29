<?php
namespace limepie;

class request
{

    public static $data;

    public static function initialize(\Closure $callback=NULL)
    {

        if(!\limepie\framework::getInstance()->getRouter())
        {

            $caller = debug_backtrace()[0];
            throw new \Exception("router를 세팅하신후 실행해주세요, called in ".$caller['file'].' on line '.$caller['line']);

        }

        self::$data = [
            "get"       => $_GET,
            "post"      => $_POST,
            "cookie"    => isset($_COOKIE)   ? $_COOKIE  : [],
            "session"   => isset($_SESSION)  ? $_SESSION : [],
            "server"    => isset($_SERVER)   ? $_SERVER  : [],
            "parameter" => \limepie\framework::getInstance()->getRouter()->getParameters(),
            "argument"  => \limepie\framework::getInstance()->getRouter()->getArguments(),
            "segment"   => \limepie\framework::getInstance()->getRouter()->getSegments()
        ];
        if($callback)
        {
            return $callback();
        }

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
        else if(!self::$data)
        {
            throw new \Exception('\limepie\request::initialize() 가 실행되지 않았습니다.');
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

    public static function has($key)
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

        return self::unsafest('REQUEST_METHOD', function($val) {
            return strtolower($val) == 'post';
        });

    }

    public static function isGet()
    {

        return self::unsafest('REQUEST_METHOD', function($val) {
            return strtolower($val) == 'get';
        });

    }

    public static function hasFiles()
    {



    }

    public static function isAjax()
    {

        return self::unsafest('HTTP_X_REQUESTED_WITH', function($val) {
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