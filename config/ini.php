<?php

namespace limepie\config;

class ini
{


    public static function set($file)
    {

        //var $e

        $f = stream_resolve_include_path($file);
        if($f)
        {
            return parse_ini_file($f, TRUE);
        }
        else
        {
            $caller = debug_backtrace()[0];
            trigger_error("file does not exists: ". $file ." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_ERROR);
        }

    }

    public static function get($file)
    {

        //var $e

        $f = stream_resolve_include_path($file);
        if($f)
        {
            return parse_ini_file($f, TRUE);
        }
        else
        {
            $caller = debug_backtrace()[0];
            trigger_error("file does not exists: ". $file ." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_ERROR);
        }

    }

}