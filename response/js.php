<?php

namespace limepie\response;
use limepie\response;

class js extends response
{

    public static function log($msg)
    {

        return '<script type="text/javascript">console.log("'.$msg.'")</script>';

    }

    public static function redirect($strUrl, $strMsg='')
    {

        $returnString = '';
        if(FALSE === empty($strMsg))
        {
            $returnString .= static::alert($strMsg);
        }

        switch($strUrl)
        {
            case 'history.back':
                $returnString .= '<script type="text/javascript">history.back();</script>';
                break;
            default:
                $returnString .= '<script type="text/javascript">window.location.href="'.$strUrl.'";</script>';
        }

        return $returnString;

    }

    public static function alert($strMsg)
    {

        return '<script type="text/javascript">alert("'.$strMsg.'");</script>';

    }

}