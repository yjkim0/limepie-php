<?php

namespace limepie;

class http
{

    public static function get_web_page( $url )
    {

        $options = array(
            CURLOPT_RETURNTRANSFER => TRUE,     // return web page
            CURLOPT_HEADER         => FALSE,    // don't return headers
            CURLOPT_FOLLOWLOCATION => TRUE,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "php", // who am i
            CURLOPT_AUTOREFERER    => TRUE,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        return $header['content'] = $content;
        return $header;

    }

}