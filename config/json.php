<?php

namespace limepie\config;

class json
{

    public static function set($file)
    {

        //var $e, $a, $error;

        try
        {
            $f = stream_resolve_include_path($file);
            if(file_exists($f) && is_readable($f))
            {
                $a = json_decode(file_get_contents($f), TRUE);

                $error = json_last_error();
                if ($error)
                {
                    switch ($error)
                    {
                        case JSON_ERROR_DEPTH:
                            $msg = "Maximum stack depth exceeded";
                            break;
                        case JSON_ERROR_STATE_MISMATCH:
                            $msg = "Underflow or the modes mismatch";
                            break;
                        case JSON_ERROR_CTRL_CHAR:
                            $msg = "Unexpected control character found";
                            break;
                        case JSON_ERROR_SYNTAX:
                            $msg = "Syntax error, malformed JSON";
                            break;
                        case JSON_ERROR_UTF8:
                            $msg = "Malformed UTF-8 characters, possibly incorrectly encoded";
                            break;
                    }
                    throw new \limepie\config\Exception($msg);
                }
                else
                {
                    return $a;
                }
            }
            else
            {
                throw new \limepie\config\Exception($file . "이 없습니다.");
            }
        }
        catch (\Exception $e)
        {
            throw new \limepie\config\Exception($e);
        }

    }

}