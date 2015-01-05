<?php

namespace limepie;

class validator
{

    private $data;
    private $rules;
    private $messages;
    private static $defaultMessages;
    private static $methods = "";//[];
    private $errors;

    private function __construct($data, $rules, $messages)
    {

        $this->data     = $data;
        $this->rules    = $rules;
        $this->messages = $messages;
        $this->errors   = [];
        \limepie\config::get("validator-method");

    }

    public static function addMethod($methodName, $method, $message='')
    {

        self::$defaultMessages[$methodName] = $message;
        self::$methods[$methodName] = $method;

    }

    public static function validate($options)
    {

        if (gettype($options) != "array")
        {
            throw new \limepie\validator\Exception("parameter error");
        }

        if (TRUE === isset($options["filter"]) && TRUE === isset($options["filter"]["rules"]))
        {
            $rules = $options["filter"]["rules"];
        }
        else
        {
            if(TRUE === isset($options["rules"]))
            {
                $rules = $options["rules"];
            }
            else
            {
                throw new \limepie\validator\Exception("not found rules");
            }
        }

        if (TRUE === isset($options["filter"]) && TRUE === isset($options["filter"]["messages"]))
        {
            $messages = $options["filter"]["messages"];
        }
        else
        {
            if(TRUE === isset($options["messages"]))
            {
                $messages = $options["messages"];
            }
            else
            {
                $messages = [];
            }
        }

        if (TRUE === isset($options["data"]))
        {
            $data = $options["data"];
        }
        else
        {
            throw new \limepie\validator\Exception("not found data");
        }

        return new \limepie\Validator($data, $rules, $messages);

    }

    public function field($name)
    {

        $value = NULL;
        if (TRUE === isset($this->data[$name]))
        {
            $value = $this->data[$name];
        }

        $rule  = NULL;
        if (TRUE === isset($this->rules[$name]))
        {
            $rule = $this->rules[$name];
        }
        if (!$rule)
        {
            return FALSE;
        }

        $result  = TRUE;
        $message = "";

        foreach($rule as $methodName => $param)
        {
            $valid   = FALSE;
            $method  = "";

            if (TRUE === isset(self::$methods[$methodName]))
            {
                $method = self::$methods[$methodName];
                if((TRUE === is_null($method) || $method($this, $value, $param)))
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
                $message = "";
                if (TRUE === isset($this->messages[$name])
                    && TRUE === isset($this->messages[$name][$methodName]))
                {
                    $message = $this->messages[$name][$methodName];
                } else if(TRUE === isset(self::$defaultMessages[$methodName]))
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
                $this->addError($name, $methodName, $param, $message);
                $result = FALSE;
            }
        }
        return $result;

    }

    public function addError($name, $methodName, $param, $message)
    {

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

    public function checkRules()
    {

        $valid = TRUE;

        foreach ($this->rules as $name => $value)
        {
            $this->field($name);
        }
        if ($this->errorCount() == 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }

    }

    public function getData() {
        return $this->data;
    }

    public function optional($value)
    {

        if (TRUE === is_null($value) || $value === '' )
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }

    }

}
