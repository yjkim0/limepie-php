<?php

namespace limepie;

class toolkit
{

    public static $times ;
    public static $standard;
    public static $prev = 0;

    public static function timer($file = "", $line = 0)
    {

        if (!self::$standard)
        {
            self::$standard = self::getMicrotime();
            self::$times = [];
            return;
        }

        //var $currentTimer, $current, $diff, $ret, $times;
        $currentTimer = self::getMicrotime();

        $current      = sprintf("%.4f", $currentTimer[1] - self::$standard[1] + $currentTimer[0] - self::$standard[0]);

        $diff         = sprintf("%.4f",$current - self::$prev);

        $ret          = "prev => ".str_pad(self::$prev,6,"0", STR_PAD_RIGHT)
                     .", current => ".str_pad($current,6,"0", STR_PAD_RIGHT)
                     .", diff => ".str_pad($diff,6,"0", STR_PAD_RIGHT);

        if ($file)
        {
            $ret = $ret.", file ".$file;
        }
        if ($line)
        {
            $ret = $ret.", line ".$line;
        }
        self::$prev = $current;

        $times = self::$times;
        $times = array_merge($times, [$ret]);
        self::$times = $times;

        return $ret;

    }

    public static function getTime()
    {

        return self::$times;

    }

    public static function getMicrotime()
    {

        //var $tmp;
        $tmp = explode(" ", microtime());
        return $tmp;

    }

}
