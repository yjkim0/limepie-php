<?php

namespace limepie;

class response
{

    public static function json($arr)
    {

        return json_encode($arr);

    }

    public static function jsonp($arr)
    {

        $callback = request\sanitize::request('callback', 'string');
        $output   = static::json($arr);

        if ($callback)
        {
            header('Content-Type: text/javascript');
            return $callback . '(' . $output . ');';
        }
        else
        {
            header('Content-Type: application/x-json');
            return $output;
        }

    }

    public static function header($content = '', $status = 200, $headers = [])
    {

    }

    public static function redirect($url)
    {

        header('HTTP/1.1 301 Moved Permanently');
        header('Location: '.$url);
        die();

    }

    public static function forward($router)
    {

    }

}
