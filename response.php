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

        $callback = request\sanitize::get('callback', 'string', function() {
            return request\sanitize::post('callback', 'string');
        });

        $output = static::json($arr);

        if ($callback) {
            header('Content-Type: text/javascript');
            return $callback . '(' . $output . ');';
        } else {
            header('Content-Type: application/x-json');
            return $output;
        }

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