<?php

use \limepie\validator;

validator::addMethod('required', function(\limepie\validator $validator, $value, $param) {

    $required = $param && $value ? TRUE : FALSE;
    return $required ? !$validator->optional($value) : TRUE;

});

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

validator::addMethod('dateISO', function(\limepie\validator $validator, $value) {

    return $validator->optional($value) || preg_match('/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/', $value);

});

validator::addMethod('number', function(\limepie\validator $validator, $value) {

    return $validator->optional($value) || is_numeric($value);

});

validator::addMethod('digits', function(\limepie\validator $validator, $value) {

    return $validator->optional($value) || preg_match('/^\d+$/', $value);

});

validator::addMethod('creditcard', function(\limepie\validator $validator, $value) {

    if ($validator->optional($value))
    {
        return TRUE;
    }

    if (preg_match('/[^0-9 \-]+/', $value))
    {
        return FALSE;
    }

    $value = preg_replace('/\/D/', '', $value);
    $check = 0;
    $even = FALSE;

    for ($n = strlen($value) - 1; $n >= 0; $n--)
    {
        $digit = intval($value[$n]);

        if ($even && ($digit *= 2) > 9)
        {
            $digit -= 9;
        }

        $check += $digit;
        $even = !$even;
    }

    return ($check % 10) === 0;

});

validator::addMethod('equalTo', function(\limepie\validator $validator, $value, $param) {

    if ($validator->optional($value))
    {
        return TRUE;
    }

    $valid = FALSE;
    $parts = validator::parseSelector($param);

    if ($parts !== NULL)
    {
        $name = $parts['name'];

        $model = $validator->getData();
        $valid = $value === (isset($model[$name]) ? $model[$name] : NULL);
    }

    return $valid;

});

validator::addMethod('password', function(\limepie\validator $validator, $value, $param) {

    return $validator->optional($value) || preg_match("#^(?=^.{6,12}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[".preg_quote("!@#$%^&amp;*()_+}{&quot;:;'?/&gt;.&lt;,",'#')."])(?!.*\s).*$#", $value);

});

