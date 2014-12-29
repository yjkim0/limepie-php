<?php
namespace limepie\request;
use limepie\request;

class cookie extends request
{

    public static function set($key, $value)
    {

        return parent::$data['cookie'][$key] = $_COOKIE[$key] = $value;

    }

}