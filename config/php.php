<?php

namespace limepie\config;

class php
{

    public static function set($file)
    {

        //var $e, $config;

        $f = stream_resolve_include_path($file);
        if($f)
        {
            $config = require $f;
            if($config)
            {
                \limepie\config::set($config);
            }
        }
        else
        {
            $caller = debug_backtrace()[0];
            trigger_error("file does not exists: ". $file ." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_ERROR);
        }

    }

}