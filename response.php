<?php

namespace limepie;

class response
{

    public static function json($arr)
    {

        return json_encode($arr);

    }

    public static function jsonp($arr)
    {

        $callback = request\get::unsafest('callback', function($val = '') {
            if($val)
            {
                return $val;
            }
            return request\post::unsafest('callback', function($val = '') {
                return $val;
            });
        });

        return $callback
                ? $callback.'('.self::json($arr).');'
                : self::json($arr);

    }

    public static function jsredirect($strUrl, $strMsg='')
    {
        if(FALSE === empty($strMsg))
        {
            echo '<script type="text/javascript">alert("'.$strMsg.'");</script>';
        }
        if(FALSE === empty($strUrl))
        {
            echo '<script type="text/javascript">window.location.href="'.$strUrl.'";</script>';
            exit();
        }
    }

}