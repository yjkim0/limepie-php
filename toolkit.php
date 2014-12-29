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

    public static function readableSize($size)
    {

        //var $i;
        $unit=["B", "kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
        $i = floor(log($size,1024));

        return round($size/pow(1024,$i),2).$unit[$i];

    }

    public static function longToIp($proper)
    {

        if ($proper < 0 || $proper > 4294967295)
        {
            return FALSE;
        }
        //var $tmp;
        $tmp = 4294967295 - ($proper - 1);
        return long2ip(-1 * $tmp);

    }

    public static function ipToLong($ip)
    {

        //var $tmp;
        $tmp = ip2long($ip);
        if ($tmp == -1 || $tmp === FALSE)
        {
            return FALSE;
        }
        return sprintf("%u", $tmp);

    }

    public static function getCountdown(int $rem, $pad = FALSE)
    {

        //var $day, $hr, $min, $sec;

        $day = floor($rem / 86400);
        $hr  = floor($rem % 86400 / 3600);
        $min = floor($rem % 86400 / 60);
        $sec = ($rem % 60);

        if ($pad == FALSE)
        {
            return [
                "day" => $day
                , "hour" => $hr
                , "min" => $min
                , "sec" => $sec
            ];
        }

        return [
            "day" => $day
            , "hour" => str_pad($hr, 2, "0", STR_PAD_LEFT)
            , "min" => str_pad($min, 2, "0", STR_PAD_LEFT)
            , "sec" => str_pad($sec, 2, "0", STR_PAD_LEFT)
        ];

    }

    public static function toDate($date)
    {

        return str_replace([
            "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
        ], [
            "일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"
        ], date("Y년 m월 d일 l", strtotime($date)));

    }

}
