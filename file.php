<?php

namespace limepie;

class file
{

    public static function makeDir($path, $permission = 0777)
    {

        $dir = '';

        if (is_dir($path)) return $path;
        $dirs=explode(DIRECTORY_SEPARATOR, $path);

        $is_create_dir = FALSE;
        foreach($dirs as $i => $value)
        {
            $dir .= $value.DIRECTORY_SEPARATOR;
            if ($is_create_dir === TRUE || (!is_dir($dir) && $is_create_dir = TRUE))
            {
                if(mkdir($dir, $permission))
                {
                }
                else
                {
                    //pr($dir);
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

    public static function delDir($path, $php_safe_mode = FALSE)
    {

        if (!$php_safe_mode)
        {
            substr(__file__,0,1)==='/'
                ? @shell_exec('rm -rf "'.$path.'/"*')
                : @shell_exec('del "'.str_replace('/','\\',$path).'\\*" /s/q');
            return;
        }
        if (!$d = @dir($path)) return;
        while ($f = @$d->read())
        {
            switch ($f)
            {
                case '.': case '..': break;
                default : @is_dir($f=$path.'/'.$f) ? $this->del_dir($f, 1) : @unlink($f);
            }
        }

    }

    public static function dirScan($dir)
    {

        $var = array();
        if (TRUE === is_dir($dir))
        {
            if ($dh = opendir($dir))
            {
                while (($file = readdir($dh)) !== FALSE)
                {
                    if(filetype($dir . $file) == "dir" && !preg_match('/^\./',$file))
                    {
                        $var[] = $file;
                    }
                }
                closedir($dh);
            }
        }
        return $var;

    }

}