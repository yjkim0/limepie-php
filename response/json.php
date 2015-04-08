<?php

namespace limepie\response;
use limepie\response;

class json extends response
{

    public static function success($msg, $data=[])
    {

        $data['message'] = $msg;
        return parent::json([
            'status' => 'success',
            'result' => $data
        ]);

    }

    public static function error($msg, $data=[])
    {

        $data['message'] = $msg;
        return parent::json([
            'status' => 'error',
            'result' => $data
        ]);

    }

    public static function validator($data=[])
    {

        return parent::json([
            'status' => 'validator',
            'result' => $data
        ]);

    }

}