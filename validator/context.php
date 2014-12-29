<?php

namespace limepie\validator;

final class Context
{

    private $validator;

    public function __construct(\limepie\validator $validator)
    {

        $this->validator = $validator;

    }

    public function optional($value)
    {

        if (is_null($value) || $value === "" )
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }

    }

    public function xparseSelector($selector)
    {

        $result = [
            "name"        => substr($selector,1),
            "pseudo-class"  => ""
        ];

        return $result;

    }

    public function parseSelector($selector)
    {

        $selector = str_replace("\\[", "[", $selector);
        $selector = str_replace("\\]", "]", $selector);

        $result = [];
        $matches = [];
        if (preg_match("/^#([A-Za-z][\\w\\-\\.]*)(:\\w+)?$/", $selector, $matches) ||
            preg_match("/^\\[name=([\\w\\-\\.\\[\\]]+)\\](:\\w+)?$/", $selector, $matches))
        {
            $result = [
                "name"        => $matches[1],
                "pseudo-class"  => isset($matches[2]) ? $matches[2] : NULL
            ];
        }
        return result;

    }

    public function resolve($value, $param)
    {

        $result = FALSE;
        if (is_bool($param))
        {
            $result = $param;
        }
        else
        {
            if (is_string($param))
            {
                $result = $this->resolveExpression($param);
            }
            else
            {
                if (is_callable($param))
                {
                    $result = $param($this->validator, $value);
                }
            }
        }
        return $result;

    }

    private function resolveExpression($expression)
    {

        $result = FALSE;
        $parts = $this->parseSelector($expression);
        if ($parts)
        {
            $name           = $parts["name"];
            $pseudo_class   = $parts["pseudo-class"];
            $model         = $this->validator->getModel();
            switch ($pseudo_class)
            {
                case "checked":
                    $result = array_key_exists($name, $model);
                    break;
                case "unchecked":
                    $result = !array_key_exists($name, $model);
                    break;
                case "filled":
                    $result = array_key_exists($name, $model) && strlen($model[$name]) > 0;
                    break;
                case "blank":
                    $result = array_key_exists($name, $model) && strlen($model[$name]) === 0;
                    break;
                case NULL:
                    // No pseudo-class.
                    $result = array_key_exists($name, $model);
                    break;
                default:
                    // Unsupported pseudo-class.
                    $result = array_key_exists($name, $model);
                    break;
            }
        }
        return $result;

    }

    public function getValidator()
    {

        return $this->validator;

    }

}