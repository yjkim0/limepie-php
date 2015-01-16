<?php

namespace limepie;

class config
{

    private static $result;
    private static $temp;

    public static function defined($name)
    {

        return isset(self::$temp[$name]);

    }

    public static function set($name, $callback = FALSE)
    {

        if(FALSE === is_array(self::$temp))
        {
            self::$temp = [];
        }
        if (TRUE === is_array($name))
        {
            self::$temp = array_merge(self::$temp, $name);

            if(TRUE === isset(self::$temp['bootstrap']) && self::$temp['bootstrap'])
            {
                self::get('bootstrap');
                unset(self::$temp['bootstrap']);
            }
        }
        else
        {
            self::$temp[$name] = $callback;
        }

    }

    public static function get($name, $sub = NULL, $sub2 = NULL)
    {

        //var $result;
        if (FALSE === isset(self::$temp[$name]))
        {
            $caller = debug_backtrace()[0];
            trigger_error("Undefined variable: config ".$caller["args"][0]." in ".$caller['file'].' on line '.$caller['line'].' and defined ', E_USER_NOTICE);

            //throw new \limepie\config\Exception($name."값이 없습니다.");
        }
        if (FALSE === isset(self::$result[$name]) && TRUE === isset(self::$temp[$name]))
        {
            $definition = self::$temp[$name];

            if ((gettype($definition) == "object") && ($definition instanceof \Closure))
            {
                self::$result[$name] = $definition();
            }
            else
            {
                self::$result[$name] = $definition;
            }
        }

        if ($sub2)
        {
            if (TRUE === isset(self::$result[$name][$sub][$sub2])
                && self::$result[$name][$sub][$sub2])
            {
                return self::$result[$name][$sub][$sub2];
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            if ($sub)
            {
                if (TRUE === isset(self::$result[$name][$sub]) && self::$result[$name][$sub])
                {
                    return self::$result[$name][$sub];
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                if (TRUE === isset(self::$result[$name]) && self::$result[$name])
                {
                    return self::$result[$name];
                }
                else
                {
                    return FALSE;
                }
            }
        }

    }

    public static function import($name)
    {

        if (FALSE === isset(self::$temp[$name]))
        {
            $caller = debug_backtrace()[1];
            throw new \limepie\config\Exception($name." 값이 셋팅되지 않았습니다.");
        }
        return self::$temp[$name];

    }

    public static function environment($domains, $func)
    {

        $match = FALSE;
        foreach($domains as $environment => $domain)
        {
            // HTTP_HOST와 매칭되는 도메인을 찾는다.
            if(TRUE === in_array(getenv('HTTP_HOST'), $domain))
            {
                \limepie\config::set('ENVIRONMENT', $environment);
                // environment별 환경설정 파일 인클루드
                $func($environment);
                $match = TRUE;
                break;
            }
        }
        if(FALSE === $match)
        {
            throw new \limepie\config\exception("domain Configuration file does not exists");
        }

    }
}