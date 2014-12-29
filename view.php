<?php

namespace limepie;

class view
{

    public static function show($define = FALSE, $display = FALSE)
    {

        $definition = config::import('template');
        if (gettype($definition) == "object" && ($definition instanceof \Closure))
        {
            return $definition($define, $display);
        }

        // default
        $tpl = new view\php;
        $tpl->define(space::name("__define__")->getAttributes());
        $tpl->assign(space::name("__assign__")->getAttributes());

        if (is_array($define))
        {
            $tpl->define($define, $display);
        }
        else
        {
            $display = $define;
        }

        return $tpl->show("layout", $display);

    }

    public static function assign($arg = [], $val = NULL)
    {

        return space::name("__assign__")->setAttribute($arg, $val);

    }

    public static function set($arg = [], $val = NULL)
    {

        return self::assign($arg, $val) ;

    }

    public static function get($attr = NULL, $key = NULL)
    {

        return space::name("__assign__")->getAttribute($attr, $key);

    }

    public static function define($arg = [], $val = NULL)
    {

        return space::name("__define__")->setAttribute($arg, $val);

    }

    public static function getAssign()
    {

        return space::name("__assign__")->getAttributes();

    }

    public static function getDefine()
    {

        return space::name("__define__")->getAttributes();

    }

}

