<?php
namespace limepie\request;
use limepie\request;

class session extends request
{

    public static function set($key, $value)
    {

        return parent::$data['session'][$key] = $_SESSION[$key] = $value;

    }

}