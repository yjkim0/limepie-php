<?php

namespace limepie\config;

class json
{

    /* setting 하고 return 하지 않음 */
    public static function set($file)
    {

        //var $e, $config, $error;

        $f = stream_resolve_include_path($file);
        if($f)
        {
            $config = json_decode(file_get_contents($f), TRUE);
            if (json_last_error())
            {
                $caller = debug_backtrace()[0];
                trigger_error(json_last_error_msg().": ". $file ." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_ERROR);
            }
            else
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

    /* setting 안하고 return 만 함 */
    public static function get($file, $decode = TRUE)
    {

        //var $e, $config, $error;

        if(FALSE !== $decode)
        {
            $decode = TRUE;
        }

        $f = stream_resolve_include_path($file);
        if($f)
        {
            $config = file_get_contents($f);
            if(TRUE === $decode)
            {
                $config = json_decode($config, TRUE);
                if (json_last_error())
                {
                    $caller = debug_backtrace()[0];
                    trigger_error(json_last_error_msg().": ". $file ." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_ERROR);
                }
                else
                {
                    return $config;
                }
            }
            else
            {
                return $config;
            }
        }
        else
        {
            $caller = debug_backtrace()[0];
            trigger_error("file does not exists: ". $file ." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_ERROR);
        }

    }

}