<?php

namespace limepie;

class func
{

    public static $times ;
    public static $standard;
    public static $prev = 0;

    public static function strtocamel($str, $capitalise_first_char = false) {

        if($capitalise_first_char)
        {
          $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);

    }

    public static function str_add_query($oldQueryString, $add = [])
    {

        parse_str($oldQueryString, $qs);
        return http_build_query(array_merge($qs, $add));

    }

    public static function file_force_contents($dir, $contents)
    {

        $parts = explode('/', ltrim($dir,'/'));

        $file = array_pop($parts);
        $dir = '';
        foreach($parts as $part)
        {
            if(!is_dir($dir .= "/$part"))
            {
                mkdir($dir);
            }
        }
        return file_put_contents("$dir/$file", $contents);

    }

    public static function readableSize($size)
    {

        //var $i;
        $unit=["B", "kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
        $i = floor(log($size,1024));

        return round($size/pow(1024,$i),2).$unit[$i];

    }

    public static function long2ip($proper)
    {

        if ($proper < 0 || $proper > 4294967295)
        {
            return FALSE;
        }
        //var $tmp;
        $tmp = 4294967295 - ($proper - 1);
        return long2ip(-1 * $tmp);

    }

    public static function ip2long($ip)
    {

        //var $tmp;
        $tmp = ip2long($ip);
        if ($tmp == -1 || $tmp === FALSE)
        {
            return FALSE;
        }
        return sprintf("%u", $tmp);

    }

}
