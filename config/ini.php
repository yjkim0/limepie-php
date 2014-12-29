<?php

namespace limepie\config;

class ini
{


    public static function set($file)
    {

        //var $e
        try
        {
            $f = stream_resolve_include_path($file);
            if(file_exists($f) && is_readable($f))
            {
                return parse_ini_file($f, TRUE);
            }
            else
            {
                throw new \limepie\config\Exception($file . " 파일이 없습니다.");
            }
        }
        catch (\Exception $e)
        {
            throw new \limepie\config\Exception($e);
        }

    }

}