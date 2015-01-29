<?php
namespace limepie;

class request
{

    public static $data;

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

    // '', false는 true, 나머지 false
    public static function isEmpty($val) {
        if(
            //TRUE === is_bool($val)
            TRUE === $val
            || TRUE === is_array($val) && TRUE === isset($val[0])
            || TRUE === is_object($val)
            || FALSE === empty($val)
            || TRUE === is_null($val)
            || TRUE === is_numeric($val)
            || TRUE === is_string($val) && 0 < strlen($val)
        )
        {
            return FALSE;
        }
        return TRUE;
    }

    public static function addData($dataName, $data)
    {

        self::$data[$dataName] = $data;

    }

    public static function getData($dataName, $key)
    {

        return TRUE === isset(self::$data[$dataName][$key]) ? self::$data[$dataName][$key] : NULL;

    }

    public static function isPost()
    {

        return strtolower(request\sanitize::server('REQUEST_METHOD', 'string')) == 'post';

    }

    public static function isGet()
    {

        return strtolower(request\sanitize::server('REQUEST_METHOD', 'string')) == 'get';

    }

    public static function hasFiles()
    {



    }

    public static function isAjax()
    {

        return strtolower(request\sanitize::server('HTTP_X_REQUESTED_WITH', 'string')) == 'xmlhttprequest';

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
