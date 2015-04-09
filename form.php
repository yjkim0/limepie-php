<?php

namespace limepie;

class form
{

    public static function getFilter($schema)
    {

        $filter = ['rules' => [], 'messages' => []];
        foreach ($schema as $propertyName => $propertySchema)
        {
            if(TRUE === isset($propertySchema['rules']))
            {
                foreach($propertySchema['rules'] as $method => $value)
                {
                    if(TRUE === isset($value['param']))
                    {
                        $filter['rules'][$propertySchema['name']][$method] = $value['param'];
                    }
                    if(TRUE === isset($value['message']))
                    {
                        $filter['messages'][$propertySchema['name']][$method] = $value['message'];
                    }
                }
            }
        }
        return $filter;

    }

    public static function getHtml($schema)
    {

        $html  = [];
        foreach ($schema as $propertyName => $propertySchema)
        {
            $html[] = static::parser($propertySchema);
        }
        return implode("", $html);

    }

    public static function render($schema)
    {

        return [
            'filter' => static::getFilter($schema),
            'html'   => static::getHtml($schema)
        ];

    }

    private static function parser($schema)
    {

        extract($schema);
        if(FALSE === isset($id))
        {
            $id = 'id'.substr(md5(rand()),0,5);
        }
        if(FALSE === isset($value))
        {
            $value = NULL;
        }
        if(FALSE === isset($note))
        {
            $note = NULL;
        }
        if($note && FALSE === is_array($note))
        {
            $note = [$note];
        }
        $template = 'form/'.$schema['type'].'.phtml';
        ob_start();
        require($template);
        $fetched = ob_get_contents();
        ob_end_clean();
        return $fetched;

    }

}