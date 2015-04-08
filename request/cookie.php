<?php
namespace limepie\request;
use limepie\request;

class cookie extends request
{

    public static function set($key, $value, $expire = 0, $path = NULL, $domain = NULL, $secure = FALSE, $httponly = FALSE)
    {

        setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
        return parent::$data['cookie'][$key] = $_COOKIE[$key] = $value;

    }

}