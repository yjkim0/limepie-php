<?php

namespace limepie\cache;

class redis
{

    public static function get($options = [])
    {

        if (!isset($options["expire"]))
        {
            $options["expire"] = 3600;
        }
        if (!isset($options["server"]))
        {
            throw new \limepie\cache\Exception("server not found");
        }
        if (!isset($options["key"]))
        {
            throw new \limepie\cache\Exception("key not found");
        }
        if (!isset($options["value"]) || gettype($options["value"]) != "object")
        {
            throw new \limepie\cache\Exception("callback function not found");
        }

        $definition = $options["value"];

        if ((gettype($definition) == "object") && ($definition instanceof \Closure))
        {
            $data = $definition();
        }
        else
        {
            $data = $definition;
        }

        return $data;

    }

}