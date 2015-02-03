<?php

namespace limepie;

class validator
{

    private $data;
    private $rules;
    private $messages;
    private static $defaultMessages;
    private static $methods = [];
    private $errors;

    private function __construct($data, $rules, $messages)
    {

        $this->data     = $data;
        $this->rules    = $rules;
        $this->messages = $messages;
        $this->errors   = [];
        \limepie\config::get("validator-method");

    }

    public static function validate($data=[], $rules=[], $messages=[])
    {

        return new \limepie\Validator($data, $rules, $messages);

    }

    public static function addMethod($methodName, $method, $message='')
    {

        self::$defaultMessages[$methodName] = $message;
        self::$methods[$methodName] = $method;

    }

    public function checkRules()
    {

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
                    if($method($this, $value, $param))
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
