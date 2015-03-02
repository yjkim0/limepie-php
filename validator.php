<?php

namespace limepie;

//\limepie\config\php::get(\limepie\config::get("validator-method"));

require_once(__DIR__.'/validator/method.php');

if(\limepie\config::defined("validator-method-path"))
{
    require_once(\limepie\config::get("validator-method-path"));
}

class validator
{

    private $data;
    private $rules;
    private $messages;
    private $errors = [];

    private static $defaultMessages = [
        'required'    => "필수 항목입니다.",
        'password'    => "비밀번호는 소문자, 대문자, 숫자, 특수문자를 모두 포함하는 6~10자 길이의 문자열이어야 합니다.",
        'maxlength'   => "{0} 자 이내로 입력해주세요.",
        'minlength'   => "{0} 자 이상 입력해주세요.",
        'rangelength' => "{0} ~ {1}자 길이의 문자를 입력해주세요.",
        'range'       => "{0}, {1} 사이의 값을 입력해주세요.",
        'max'         => "{0} 이하의 값을 입력해주세요.",
        'min'         => "{0} 이상의 값을 입력해주세요.",
        'email'       => "유효한 E-메일 주소를 입력해주세요.",
        'url'         => "유효한 URL을 입력해주세요.",
        'equalTo'     => "{0} 항목과 동일한 값을 입력해주세요.",
        'date'        => "유효한 날짜를 입력해주세요.",
        'number'      => "유효한 숫자를 입력해주세요.",
        'digits'      => "숫자만 입력해주세요."
    ];

    private static $methods = [];

    private function __construct($data, $rules, $messages='')
    {

        if($data)     $this->data     = self::keyFlatten($data);
        if($rules)    $this->rules    = $rules;
        if($messages) $this->messages = $messages;

        foreach(self::$methods as $methodName => $method) {
            static::$methods[$methodName] = \Closure::bind($method, $this);
        }

    }

    public static function validate($data=[], $rules=[], $messages=[])
    {

        return new \limepie\Validator($data, $rules, $messages);

    }

    public static function addMethod($methodName, \Closure $methodCallable, $message='')
    {

        if($message) self::$defaultMessages[$methodName] = $message;
        static::$methods[$methodName] = $methodCallable;

    }

    private function keyFlatten($data) {

        $isChild = FALSE;
        $return = [];
        foreach ($data as $name => $value) {
            if (TRUE === is_array($value)) {
                foreach ($value as $subName => $subValue) {
                    if(TRUE === is_numeric($subName))
                    {
                        $return[$name][$subName] = $subValue;
                    }
                    else
                    {
                        $isChild = TRUE;
                        $return[$name.'['.$subName.']'] = $subValue;
                    }
                }
            } else {
                $return[$name] = $value;
            }
        }
        if (TRUE === $isChild) {
            $return = self::keyFlatten($return);
        }

        return $return;

    }

    public function checkRules()
    {

        $this->errors   = [];
        $result  = TRUE;
        foreach ($this->rules as $name => $rules)
        {

            $value = NULL;

            if (TRUE === isset($this->data[$name]))
            {
                $value = $this->data[$name];
            }

            foreach($rules as $methodName => $param)
            {
                $valid   = FALSE;
                if (TRUE === isset(self::$methods[$methodName]))
                {
                    $method = self::$methods[$methodName];

                    if(TRUE === $method($name, $value, $param))
                    {
                        $valid = TRUE;
                    }
                }
                else
                {
                    throw new \limepie\validator\Exception("not found '" . $methodName . "' validate rule");
                }

                if (FALSE === $valid)
                {
                    $this->addError($name, $methodName, $param);
                    $result = FALSE;
                }
            }
        }
        return $result;

    }

    private function addError($name, $methodName, $param)
    {

        $message = "";
        if (TRUE === isset($this->messages[$name])
            && TRUE === isset($this->messages[$name][$methodName]))
        {
            $message = $this->messages[$name][$methodName];
        }
        else if(TRUE === isset(self::$defaultMessages[$methodName]))
        {
            $message = self::$defaultMessages[$methodName];
        }

        if($message)
        {
            $p = $param;
            if (FALSE === is_array($param))
            {
                $p = [$param];
            }
            $message = [preg_replace("/\\{([0-9]+)\\}/", "%s", $message)];
            $message = call_user_func_array("sprintf", array_merge($message , $p));
        }

        $this->errors[$name][$methodName] = [
            "name"      => $name,
            "method"    => $methodName,
            "param"     => $param,
            "message"   => $message
        ];

    }

    public function errorCount()
    {

        return count($this->errors);

    }

    public function getErrors()
    {

        return $this->errors;

    }

    public function getData($param)
    {

        return TRUE === isset($this->data[$param]) ? $this->data[$param] : NULL;

    }

    public function optional($value)
    {

        if (TRUE === is_null($value) || $value == '' || !$value)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }

    }

}
