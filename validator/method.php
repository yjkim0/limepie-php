<?php

\limepie\Validator::addMethod('required', function(\limepie\Validator\Context $context, $value, $param) {

    $required = $context->resolve($value, $param);
    return $required ? !$context->optional($value) : TRUE;

});

\limepie\Validator::addMethod('minlength', function(\limepie\Validator\Context $context, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $context->optional($value) || $length >= $param;

});

\limepie\Validator::addMethod('maxlength', function(\limepie\Validator\Context $context, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $context->optional($value) || $length <= $param;

});

\limepie\Validator::addMethod('rangelength', function(\limepie\Validator\Context $context, $value, $param) {

    $length = is_array($value) ? count($value) : strlen($value);
    return $context->optional($value) || $length >= $param[0] && $length <= $param[1];

});

\limepie\Validator::addMethod('min', function(\limepie\Validator\Context $context, $value, $param) {

    return $context->optional($value) || $value >= $param;

});

\limepie\Validator::addMethod('max', function(\limepie\Validator\Context $context, $value, $param) {

    return $context->optional($value) || $value <= $param;

});

\limepie\Validator::addMethod('range', function(\limepie\Validator\Context $context, $value, $param) {

    return $context->optional($value) || $value >= $param[0] && $value <= $param[1];

});

\limepie\Validator::addMethod('email', function(\limepie\Validator\Context $context, $value) {

    return $context->optional($value) || filter_var($value, FILTER_VALIDATE_EMAIL) !== FALSE;

});

\limepie\Validator::addMethod('url', function(\limepie\Validator\Context $context, $value) {

    if ($context->optional($value))
    {
        return TRUE;
    }

    $hasPermittedProtocol =
            substr($value, 0, 4) === 'http' ||
            substr($value, 0, 5) === 'https' ||
            substr($value, 0, 3) === 'ftp';

    return $hasPermittedProtocol && filter_var($value, FILTER_VALIDATE_URL) !== FALSE;

});

\limepie\Validator::addMethod('date', function(\limepie\Validator\Context $context, $value) {

    return $context->optional($value) || strtotime($value) !== FALSE;

});

\limepie\Validator::addMethod('dateISO', function(\limepie\Validator\Context $context, $value) {

    return $context->optional($value) || preg_match('/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/', $value);

});

\limepie\Validator::addMethod('number', function(\limepie\Validator\Context $context, $value) {

    return $context->optional($value) || is_numeric($value);

});

\limepie\Validator::addMethod('digits', function(\limepie\Validator\Context $context, $value) {

    return $context->optional($value) || preg_match('/^\d+$/', $value);

});

\limepie\Validator::addMethod('creditcard', function(\limepie\Validator\Context $context, $value) {

    if ($context->optional($value))
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

\limepie\Validator::addMethod('equalTo', function(\limepie\Validator\Context $context, $value, $param) {

    if ($context->optional($value))
    {
        return TRUE;
    }

    $valid = FALSE;
    $parts = $context->parseSelector($param);

    if ($parts !== NULL)
    {
        $name = $parts['name'];

        $model = $context->getValidator()->getModel();
        $valid = $value === (isset($model[$name]) ? $model[$name] : NULL);
    }

    return $valid;

});

\limepie\Validator::addMethod('password', function(\limepie\Validator\Context $context, $value, $param) {

    return $context->optional($value) || preg_match("#^(?=^.{6,12}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[".preg_quote("!@#$%^&amp;*()_+}{&quot;:;'?/&gt;.&lt;,",'#')."])(?!.*\s).*$#", $value);

});

