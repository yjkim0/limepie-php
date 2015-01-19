<?php

namespace limepie\response;
use limepie\response;

class json extends response
{

    public static function success($msg, $data=array())
    {
        $data['message'] = $msg;
        return parent::json([
            'status' => 'success',
            'result' => $data
        ]);
    }

    public static function error($msg, $data=array())
    {
        $data['message'] = $msg;
        return parent::json([
            'status' => 'error',
            'result' => $data
        ]);
    }

    public static function validator($msg, $data=array())
    {
        $data['message'] = $msg;
        return parent::json([
            'status' => 'validator',
            'result' => $data
        ]);
    }
}