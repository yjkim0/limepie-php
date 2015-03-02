<?php

namespace limepie\cache;

class file
{

    public static $data = "";
    public static $ext  = ".php";

    public static function getPath()
    {
        return \limepie\config::get("cachedir");
    }

    private static function _cache_name($name)
    {

        return self::getPath().DIRECTORY_SEPARATOR."cache_".md5($name).self::$ext;

    }

    public static function _get($id)
    {

        if (TRUE === isset(self::$data[$id]))
        {
            return self::$data[$id]; // Already set, return to sender
        }

        $path       = self::_cache_name($id);
        $expires    = 0;
        $cache      = FALSE;

        // Check if the $cache file exists
        if (file_exists($path) && is_readable($path))
        {
            $tmp    = unserialize(file_get_contents($path));
            $cache  = $tmp["cache"];
            $expires= $tmp["expires"];

            if ($expires > 0 && $expires <= time())
            {
                self::clear($id);
                return FALSE;
            }
            else
            {
                return $cache;//isset($cache)? $cache : FALSE);
            }
        } else {
            return FALSE;
        }

    }

    public static function clear($id)
    {

        if (TRUE === isset(self::$data[$id]))
        {
            unset(self::$data[$id] ); // Already set, return to sender
        }

        $path = self::_cache_name($id);

        if (file_exists($path) && is_readable($path) && unlink($path))
        {
            return TRUE;
        }
        else
        {
            return FALSE; //throw new CacheException('Cache could not be cleared.');
        }

    }

    public static function put($id, $cache, $lifetime = 0)
    {

        //var $path, $fp, $tmp, $content;
        self::$data[$id] = $cache;

        if (TRUE === is_resource($cache))
        {
            throw new \limepie\cache\Exception("Can't cache resource.");
        }

        $path    = self::_cache_name($id);
        self::makeDir(dirname($path));

        $fp      = fopen($path, "w");
        if (!$fp)
        {
            throw new \limepie\cache\Exception("Unable to open file for writing.".path);
        }
        flock($fp, LOCK_EX);

        $tmp = [
            "cache" => $cache
        ];

        if ($lifetime > 0)
        {
            $tmp["expires"] = (time()+$lifetime);
        }
        else
        {
            $tmp["expires"] = 0;
        }

        $content = serialize($tmp);
        fwrite($fp, $content);
        flock($fp, LOCK_UN);
        fclose($fp);

        if (file_exists($path) && is_readable($path))
        {
            chmod($path, 0777);
        }
        else
        {
            return FALSE;
        }

        return TRUE;

    }

    public static function makeDir($path, $permission = 0777)
    {

        $dir = "";

        if (is_dir($path))
        {
            return $path;
        }

        $dirs=explode(DIRECTORY_SEPARATOR, $path);

        $is_create_dir = FALSE;

        foreach ($dirs as $i => $value)
        {
            $dir.= $value.DIRECTORY_SEPARATOR;
            if ($is_create_dir == TRUE || !is_dir($dir))
            {
                if(mkdir($dir, $permission))
                {
                    $is_create_dir = TRUE;
                }
                else
                {
                    //pr(dir);
                    // error
                }
                chmod($dir, $permission);
            }
            else
            {
                // exists
            }
        }
        return $dir;
    }

    public static function get($options = [])
    {

        if (FALSE === isset($options["expire"]))
        {
            $options["expire"] = 3600;
        }
        if (FALSE === isset($options["key"]))
        {
            throw new \limepie\cache\Exception("key not found");
        }
        if (FALSE === isset($options["value"]) || gettype($options["value"]) != "object")
        {
            throw new \limepie\cache\Exception("callback function not found");
        }

        $data = self::_get($options["key"]); // 존재확인
        if(!$data)
        {

            $definition = $options["value"];

            if ((gettype($definition) == "object") && ($definition instanceof \Closure))
            {
                $data = $definition();
            }
            else
            {
                $data = $definition;
            }

            self::put($options["key"], $data, $options["expire"]); // 생성
        }
        return $data;

    }

}