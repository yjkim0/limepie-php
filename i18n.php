<?php
namespace limepie;

class i18n
{

    public static $translate  = [];
    public static $locale     = '';
    public static $extension  = '.ini.php';
    public static $errorLevel = 7;
    public static $trace      = 0;

    const NOTICE_VAR          = 1;
    const NOTICE_LOCALE       = 2;
    const NOTICE_DIR          = 4;

    public static function _($var, $args=[])
    {

        return self::_gettext($var, $args);

    }

    public static function gettext($var, $args=[])
    {

        return self::_gettext($var, $args);

    }

    private static function _gettext($var, $args=[])
    {

        $tmp = explode('\\',$var, 3);
        $n=FALSE;
        if(TRUE===isset(self::$translate[$tmp[0]]))
        {
            $data = self::$translate[$tmp[0]];
            for($i=1,$j=count($tmp);$i<$j;$i++)
            {
                $str = $tmp[$i];
                if(TRUE===isset($data[$str]))
                {
                    $data = $data[$str];
                }
                else
                {
                    $n=TRUE;
                    break;
                }
            }
        }
        else
        {
            $n=TRUE;
        }
        if(TRUE===$n)
        {
            if(self::$errorLevel & self::NOTICE_VAR)
            {
                $caller = debug_backtrace()[1];
                trigger_error("Locale Undefined variable: ".$var." in ".$caller['file'].' on line '.$caller['line'], E_USER_NOTICE);
            }
            return $var;
        }

        if($args)
        {
            $p = $args;
            if(FALSE === is_array($args))
            {
                $p = [$args];
            }
            for($i=0,$j=count($p);$i<$j;$i++)
            {
                $data = preg_replace("/\{".$i."\}/",array_shift($p),$data);
            }
        }
        return $data;

    }

    public static function errorReporting($level)
    {

        self::$errorLevel = $level;

    }

    public static function setlocale($lang)
    {

        return self::$locale = $lang;

    }

    public static function getlocale()
    {

        return self::$locale;

    }

    public static function addDirectory($module)
    {

        $lang = self::getLocale();
        if($lang)
        {
           return self::loadFile($module.'/'.$lang.self::$extension);
        }
        else
        {
            if(self::$errorLevel & self::NOTICE_LOCALE)
            {
                $caller = debug_backtrace()[0];
                trigger_error("You are *required* to use \limepie\i18n::setLocale(LANG) method. in ".$caller['file'].' on line '.$caller['line'], E_USER_NOTICE);
            }
        }

    }

    public static function loadFile($file)
    {

        if(FALSE === isset(self::$translate) || FALSE === is_array(self::$translate))
        {
            self::$translate = [];
        }
        if($f = stream_resolve_include_path($file))
        {
            self::$translate = array_merge(parse_ini_file($f, TRUE), self::$translate);
        }
        else
        {
            if(self::$errorLevel & self::NOTICE_DIR)
            {
                $caller = debug_backtrace()[1];
                trigger_error("Locale file does not exist: ".$file." called in ".$caller['file'].' on line '.$caller['line'], E_USER_NOTICE);
            }
        }

    }

}
