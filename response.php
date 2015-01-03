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
            if($val == '')
            {
                return request\post::unsafest('callback', function($val = '') {
                    return $val;
                });
            }
            return $val;
        });

        return $callback
                ? $callback.'('.self::json($arr).');'
                : self::json($arr);

    }

}