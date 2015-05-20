<?php

namespace limepie;

class tree
{

    // default field name
    public static $field = [
        'sequence' => 'seq',
        'menu'     => 'menu',
        'url'      => 'url',
        'uri'      => 'uri',
        'parent'   => 'parent',
        'selected' => 'selected',
        'children' => 'child'
    ];

    public static function get($array, $pathinfo)
    {

        $parent    = 0;
        $newMenu   = [];
        $location  = [];
        $self      = [];

        foreach($array as $key => &$value)
        {
            if($value[static::$field['menu']])
            {
                $value[static::$field['url']] = $value[static::$field['menu']].($value[static::$field['uri']]?'/'.$value[static::$field['uri']]:'');
            }
            else
            {
                $value[static::$field['url']] = $value[static::$field['uri']];
            }

            if(preg_match('#^\b'.$value[static::$field['url']].'\b#', $pathinfo))
            {
                $value[static::$field['selected']] = TRUE;
                $parent                 = $value[static::$field['parent']];
                $location[]             = $value;
                $self                   = $value;
            }
            else
            {
                $value[static::$field['selected']] = FALSE;
            }
            if(FALSE === isset($newMenu[$value[static::$field['sequence']]]))
            {
                $newMenu[$value[static::$field['sequence']]] = $value;
            }

        }
        while($parent)
        {
            foreach($newMenu as $key => &$value)
            {
                if($value[static::$field['sequence']] == $parent)
                {
                   $value[static::$field['selected']]   = TRUE;
                   $parent              = $value[static::$field['parent']];
                   $location[]          = $value;
                   break;
                }
            }
        }

        return [
            $location,
            static::hierarchy(array_values($newMenu))
        ];

    }

    // array -> hierarchy array
    public static function hierarchy($array, $parent=0)
    {

        $ret = array();

        for($i=0; $i < count($array); $i++)
        {
            if ($array[$i][static::$field['parent']] == $parent)
            {
                $a = $array[$i];
                array_splice($array,$i--,1);

                $a[static::$field['children']] = static::hierarchy($array, $a[static::$field['sequence']]);
                $ret[$a[static::$field['sequence']]] = $a;
                continue;
            }
        }

        return $ret;

    }

}