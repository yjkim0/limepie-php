<?php

namespace limepie\config;

class json
{

    public static function set($file)
    {

        //var $e, $a, $error;

        $f = stream_resolve_include_path($file);
        if($f)
        {
            $a = json_decode(file_get_contents($f), TRUE);

            if (json_last_error())
            {
                $caller = debug_backtrace()[0];
                trigger_error(json_last_error_msg().": ". $file ." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_ERROR);
            }
            else
            {
                return $a;
            }
        }
        else
        {
            $caller = debug_backtrace()[0];
            trigger_error("file does not exists: ". $file ." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_ERROR);
        }

    }

}