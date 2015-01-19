<?php

use \limepie\validator;

validator::addMethod('required', function(\limepie\validator $validator, $value, $param) {

    $required = $param ? TRUE : FALSE;
    return $required ? !$validator->optional($value) : TRUE;

}, '필수 항목입니다. 입력해주세요.');

validator::addMethod('minlength', function(\limepie\validator $validator, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $validator->optional($value) || $length >= $param;

});

validator::addMethod('maxlength', function(\limepie\validator $validator, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $validator->optional($value) || $length <= $param;

});

validator::addMethod('rangelength', function(\limepie\validator $validator, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $validator->optional($value) || $length >= $param[0] && $length <= $param[1];

});

validator::addMethod('min', function(\limepie\validator $validator, $value, $param) {

    return $validator->optional($value) || $value >= $param;

});

validator::addMethod('max', function(\limepie\validator $validator, $value, $param) {

    return $validator->optional($value) || $value <= $param;

});

validator::addMethod('range', function(\limepie\validator $validator, $value, $param) {

    return $validator->optional($value) || $value >= $param[0] && $value <= $param[1];

});

validator::addMethod('email', function(\limepie\validator $validator, $value) {

    return $validator->optional($value) || filter_var($value, FILTER_VALIDATE_EMAIL) !== FALSE;

});

validator::addMethod('url', function(\limepie\validator $validator, $value) {

    if ($validator->optional($value))
    {
        return TRUE;
    }

    $hasPermittedProtocol =
            substr($value, 0, 4) === 'http' ||
            substr($value, 0, 5) === 'https' ||
            substr($value, 0, 3) === 'ftp';

    return $hasPermittedProtocol && filter_var($value, FILTER_VALIDATE_URL) !== FALSE;

});

validator::addMethod('date', function(\limepie\validator $validator, $value) {

    return $validator->optional($value) || strtotime($value) !== FALSE;

});

validator::addMethod('number', function(\limepie\validator $validator, $value) {

    return $validator->optional($value) || is_numeric($value);

});

validator::addMethod('digits', function(\limepie\validator $validator, $value) {

    return $validator->optional($value) || preg_match('/^\d+$/', $value);

});

validator::addMethod('equalTo', function(\limepie\validator $validator, $value, $param) {

    if ($validator->optional($value))
    {
        return TRUE;
    }

    $valid = FALSE;
    if ($param !== NULL)
    {
        $model = $validator->getData();
        $valid = $value === (isset($model[preg_replace('/^#/','',$param)]) ? $model[preg_replace('/^#/','',$param)] : NULL);
    }

    return $valid;

}, '{0}와 일치하지 않음.');

validator::addMethod('password', function(\limepie\validator $validator, $value, $param) {

    return $validator->optional($value) || preg_match("#^(?=^.{6,12}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[".preg_quote("!@#$%^&amp;*()_+}{&quot;:;'?/&gt;.&lt;,",'#')."])(?!.*\s).*$#", $value);

});

validator::addMethod('birthday', function(\limepie\validator $validator, $value) {

    return preg_match('/^\d\d\d\d\d\d\d\d$/', $value) && strtotime($value) !== FALSE && $value < date('Ymd');

});

validator::addMethod('joinage', function(\limepie\validator $validator, $value, $param) {

    return date("Y") - (int)substr($value,0,4) >= (int)$param - 1;

});

validator::addMethod('mobile', function(\limepie\validator $validator, $value, $param) {

    return $validator->optional($value) || preg_match('/(01[0,1,6,7,9])(-?)(\d{3,4})\2(\d{4})/', $value);

});

