<?php

namespace limepie\config;

class php
{

    public static function set($file)
    {

        //var $e, $config;
        try
        {
            $f = stream_resolve_include_path($file);
            if(file_exists($f) && is_readable($f))
            {
                $config = require $f;
                \limepie\config::set($config);
            }
            else
            {
                throw new \limepie\config\Exception($file.' file not found');
            }
        }
        catch (\Exception $e)
        {
            throw new \limepie\config\Exception($e);
        }

    }


}