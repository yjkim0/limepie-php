<?php

namespace limepie;

class js
{

    public static function alert($strMsg)
    {

        echo '<script type="text/javascript">alert("'.$strMsg.'");</script>';

    }

    public static function move($strUrl)
    {

        echo "<meta http-equiv='refresh' content='0; url=".$strUrl."'>";

    }

    public static function jsmove($strUrl)
    {

        echo '<script type="text/javascript">window.location.href="'.$strUrl.'";</script>';

    }

    public static function redirect($strUrl, $strMsg='')
    {

        if(FALSE === empty($strMsg))
        {
            alert($strMsg);
        }
        if(FALSE === empty($strUrl))
        {
            move($strUrl);
            exit();
        }

    }

    public static function jsredirect($strUrl, $strMsg='')
    {

        if(FALSE === empty($strMsg))
        {
            self::alert($strMsg);
        }
        if(FALSE === empty($strUrl))
        {
            self::jsmove($strUrl);
            exit();
        }

    }

    public static function submit($url, $config)
    {

        $config['method'] = is(@$config['method'], 'post');
        if(isset($config['args']) == FALSE && count($config['args']) == 0)
        {
            if('get' == $config['method']) {
                $config['args'] = $_GET;
            } else {
                $config['args'] = $_POST;
            }
        }
        if(isset($config['msg']) && $config['msg'])
        {
            alert($config['msg']);
        }
        $ret = "<script type='text/javascript'>function gosubmit(){var o = document.getElementById('jsform'); o.setAttribute('method','".$config['method']."'); o.setAttribute('action','".$url."'); o.submit(); } </script><form id='jsform'>";
        if(isset($config['args']) && count($config['args'])>0)
        {
            foreach($config['args'] as $key => $value)
            {
                $ret .= "<textarea name='".$key."' style='display:none'>".$value."</textarea>";
            }
        }
        $ret .= "</form><script type='text/javascript'>gosubmit();</script>";
        echo $ret;

    }

}