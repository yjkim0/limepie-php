<?php

namespace limepie\response;
use limepie\response;

class js extends response
{

    public static function log($msg)
    {

        return "<script>console.log('".$msg."')</script>";

    }

}