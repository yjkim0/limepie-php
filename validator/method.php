<?php

use \limepie\validator;

validator::addMethod('required', function($name, $value, $param) {

    $required = $param ? TRUE : FALSE;
    return $required ? !$this->optional($value) : TRUE;

}, '필수 항목입니다. 입력해주세요.');

validator::addMethod('minlength', function($name, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $this->optional($value) || $length >= $param;

});

validator::addMethod('maxlength', function($name, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $this->optional($value) || $length <= $param;

});

validator::addMethod('rangelength', function($name, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $this->optional($value) || $length >= $param[0] && $length <= $param[1];

});

validator::addMethod('minCount', function($name, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $this->optional($value) || $length >= $param;

});

validator::addMethod('maxCount', function($name, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $this->optional($value) || $length <= $param;

});

validator::addMethod('rangeCount', function($name, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $this->optional($value) || $length >= $param[0] && $length <= $param[1];

});

validator::addMethod('hasUpperChar', function($name, $value, $param) {

    return $this->optional($value) || preg_match('#[A-Z]#', $value);

});

validator::addMethod('hasLowerChar', function($name, $value, $param) {

    return $this->optional($value) || preg_match('#[a-z]#', $value);

});

validator::addMethod('hasSpecialChar', function($name, $value, $param) {

    return $this->optional($value) || preg_match('#[~`!\#$@%\^&*+=\-\[\]\';,/{}|":()<>\?]#', $value);

});

validator::addMethod('hasNumber', function($name, $value, $param) {

    return $this->optional($value) || preg_match('#[0-9]#', $value);

});

validator::addMethod('alpha', function($name, $value, $param) {

    return $this->optional($value) || preg_match('#^[a-z]+$#', $value);

});

validator::addMethod('remote', function($name, $value, $param) {

    if ( $this->optional($value) ) {
        return TRUE;
    }

    if(!$value) return FALSE;

    if(preg_match('#^http#i', $param))
    {
        $domain = $param;
    }
    else
    {
        $domain = \limepie\request::currentDomain(). $param;
    }
    $domain .= '?'.$name.'='.$value.'&test=7';

    $getHttp = function ($url,$params = [])
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        if($params)
        {
            $postData = http_build_query($params);
            curl_setopt($ch, CURLOPT_POST, count($postData));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $output=curl_exec($ch);
        curl_close($ch);
        return $output;

    };

    $ret = (boolean)$getHttp($domain);

    if(TRUE === $ret) {
        return TRUE;
    } else {
        return FALSE;
    }

});


validator::addMethod('min', function($name, $value, $param) {

    return $this->optional($value) || $value >= $param;

});

validator::addMethod('max', function($name, $value, $param) {

    return $this->optional($value) || $value <= $param;

});

validator::addMethod('range', function($name, $value, $param) {

    return $this->optional($value) || $value >= $param[0] && $value <= $param[1];

});

validator::addMethod('email', function($name, $value, $param) {

    return $this->optional($value) || filter_var($value, FILTER_VALIDATE_EMAIL) !== FALSE;

});

validator::addMethod('url', function($name, $value, $param) {

    if ($this->optional($value))
    {
        return TRUE;
    }

    $hasPermittedProtocol =
            substr($value, 0, 4) === 'http' ||
            substr($value, 0, 5) === 'https' ||
            substr($value, 0, 3) === 'ftp';

    return $hasPermittedProtocol && filter_var($value, FILTER_VALIDATE_URL) !== FALSE;

});

validator::addMethod('date', function($name, $value, $param) {

    return $this->optional($value) || strtotime($value) !== FALSE;

});

validator::addMethod('number', function($name, $value, $param) {

    return $this->optional($value) || is_numeric($value);

});

validator::addMethod('digits', function($name, $value, $param) {

    return $this->optional($value) || preg_match('/^\d+$/', $value);

});

validator::addMethod('equalTo', function($name, $value, $param) {

    if ($this->optional($value))
    {
        return TRUE;
    }

    $valid = FALSE;
    if ($param)
    {
        $valid = ($value === $this->getData($param));
    }

    return $valid;

}, '{0}와 일치하지 않음.');

validator::addMethod('password', function($name, $value, $param) {

    return $this->optional($value) || preg_match("#^(?=^.{6,12}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[".preg_quote("!@#$%^&amp;*()_+}{&quot;:;'?/&gt;.&lt;,",'#')."])(?!.*\s).*$#", $value);

});

validator::addMethod('birthday', function($name, $value, $param) {

    return $this->optional($value) || preg_match('/^\d\d\d\d\d\d\d\d$/', $value) && strtotime($value) !== FALSE && $value < date('Ymd');

});

validator::addMethod('joinage', function($name, $value, $param) {

    return $this->optional($value) || date("Y") - (int)substr($value,0,4) >= (int)$param - 1;

});

validator::addMethod('mobile', function($name, $value, $param) {

    return $this->optional($value) || preg_match('/(01[0,1,6,7,9])(-?)(\d{3,4})\2(\d{4})/', $value);

});

validator::addMethod('tel', function($name, $value, $param) {

    return $this->optional($value) || preg_match('/(0\d{1,2})(-?)(\d{3,4})\2(\d{4})/', $value);

});
