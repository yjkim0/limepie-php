<?php

namespace limepie;

final class Validator
{

    private $model;
    private $rules;
    private $messages;
    private $context;
    private static $methods = "";//[];
    private $error;

    private function __construct($model, $rules, $messages)
    {

        $this->model     = self::flatten($model);
        $this->rules     = $this->normalizeRules($rules);
        $this->messages  = $this->normalizeRules($messages);
        $this->context   = new \limepie\validator\context($this);
        $this->error     = [];
        \limepie\config::get("validator-method");

    }

    private static function flatten($model)
    {

        $repeat = FALSE;

        foreach($model as $key => $value)
        {
            if (is_array($value))
            {
                $repeat = TRUE;
                foreach($value as $sub_key => $sub_value)
                {
                    $model[$key[$sub_key]] = $sub_value;
                }
                unset($model[$key]);
            }
        }

        if ($repeat)
        {
            $model = self::flatten($model);
        }
        return $model;

    }

    public static function validate($options)
    {

        $rules = "";

        if (gettype($options) != "array")
        {
            throw new \limepie\validator\Exception("parameter error");
        }

        if (isset($options["filter"]) && isset($options["filter"]["rules"]))
        {
            $rules = $options["filter"]["rules"];
        }
        else
        {
            if(isset($options["rules"]))
            {
                $rules = $options["rules"];
            }
            else
            {
                throw new \limepie\validator\Exception("not found rules");
            }
        }

        if (isset($options["filter"]) && isset($options["filter"]["messages"]))
        {
            $messages = $options["filter"]["messages"];
        }
        else
        {
            if(isset($options["messages"]))
            {
                $messages = $options["messages"];
            }
            else
            {
                $messages = [];
            }
        }

        if (isset($options["data"]))
        {
            $data = $options["data"];
        }
        else
        {
            if (isset($options["method"]))
            {
                if (strtolower($options["method"]) == "post")
                {
                    $data = $_POST;
                }
                else
                {
                    $data = $_GET;
                }
            }
            else
            {
                // request method로 자동 지정하는것은 삭제해야할듯..
                /*
                if strtolower(_SERVER["REQUEST_METHOD"]) == "post" {
                    $data = _POST;
                } else {
                    $data = _GET;
                }
                */
                throw new \limepie\validator\Exception("not found data");
            }
        }

        $validator = new \limepie\Validator($data, $rules, $messages);

        if ($validator->run())
        {
            if (isset($options["success"]) && gettype($options["success"]) == "object")
            {
                $method = $options["success"];
                $method($validator);
            }
        }
        else
        {
            if (isset($options["error"]) && gettype($options["error"]) == "object")
            {
                $method = $options["error"];
                $method($validator);
            }
        }
        $a = $validator->getError();
        return $a;

    }


    private function normalizeRules($rules)
    {

        foreach($rules as $name => $rule)
        {
            $normalized_rule = $this->normalizeRule($rule);

            foreach($normalized_rule as $method_name => $param)
            {
                if ($param === FALSE)
                {
                    unset($normalized_rule[$method_name]);
                }
            }
            $rules[$name] = $normalized_rule;
        }

        return $rules;

    }


    private function normalizeRule($value)
    {

        $normalized_value = $value;

        if (is_string($value))
        {
            $normalized_value = [];
            $method_names = preg_split("/\\s/", $value);
            foreach($method_names as $method_key => $method_name)
            {
                $normalized_value[$method_name] = TRUE;
            }
        }
        return $normalized_value;

    }

    public function field($name)
    {

        if (isset($this->model[$name]))
        {
            $value = $this->model[$name];
        }
        else
        {
            $value = NULL;
        }
        if (isset($this->rules[$name]))
        {
            $rule = $this->rules[$name];
        }
        else
        {
            $rule = NULL;
        }

        $message = "";

        if (!$rule)
        {
            return FALSE;
        }

        $result = TRUE;

        foreach($rule as $method_name => $param)
        {
            $valid   = TRUE;

            if (isset(self::$methods[$method_name]))
            {
                $method = self::$methods[$method_name];
            }
            else
            {
                $method = "";
            }

            if ($method)
            {
                if((is_null($method) || $method($this->context, $value, $param)))
                {
                    $valid = TRUE;
                }
                else
                {
                    $valid = FALSE;
                }
            }
            else
            {
                throw new \limepie\validator\Exception("not found '" . $method_name . "' validate rule");
            }

            if (!$valid)
            {

                if (isset($this->messages[$name]) && isset($this->messages[$name][$method_name]))
                {

                    if (is_array($param))
                    {
                        $p = $param;
                    }
                    else
                    {
                        $p = [$param];
                    }
                    $message = preg_replace("/\\{([0-9]+)\\}/", "%s", $this->messages[$name][$method_name]);

                    $s = array_merge([$message] , $p);
                    //pr([message, s]);
                    $message = call_user_func_array("sprintf", $s);

                }
                else
                {
                    $message = "";
                }
                $this->addError($name, $method_name, $param, $message);

                $result = FALSE;
            }
        }
        return $result;

    }

    public function addError($name, $method_name, $param, $message)
    {

        $this->error[$name][$method_name] = [
            "name"      => $name,
            "method"    => $method_name,
            "param"     => $param,
            "message"   => $message
        ];

    }

    public function countError()
    {

        return count($this->error);

    }

    public function getError()
    {

        return $this->error;

    }

    public function run()
    {

        $valid = TRUE;

        foreach ($this->rules as $name => $value)
        {
            $this->field($name);
        }
        if ($this->countError() == 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }

    }

    public function invalidFields()
    {

        $invalids = [];

        foreach($this->rules as $name => $rule)
        {
            if (!$this->field($name))
            {
                $invalids[$name] = isset($this->model[$name]) ? $this->model[$name] : NULL;
            }
        }
        return $invalids;

    }

    public function numberOfInvalidFields()
    {

        return count($this->invalidFields());

    }

    public function getModel()
    {

        return $this->model;

    }

    public function getRules()
    {

        return $this->rules;

    }

    public static function getMethods()
    {

        return self::$methods;

    }

    public static function addMethod($method_name, $method)
    {

        self::$methods[$method_name] = $method;

    }

}

