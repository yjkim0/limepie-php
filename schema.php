<?php

namespace limepie;

use limepie\config;
use limepie\form;
use limepie\validator;
use limepie\tree;

class schema
{

    public static function validator($file, $data = [])
    {

        $schema = config\php::get($file);
        $filter = form::getFilter($schema);
        return validator::validate($data, $filter['rules'], $filter['messages']);

    }

    public static function render($file, $data = [])
    {

        $schema = config\php::get($file);
        return form::render($schema);

    }

    public static function tree($file, $data)
    {

        $schema = config\php::get($file);
        return tree::get($schema, $data);

    }

}