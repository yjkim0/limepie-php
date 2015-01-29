<?php

namespace limepie;

class jsonSchemaForm
{

    public $schema;
    public $data;
    public $options;

    public function __construct($schema, $options = [], $data = [])
    {

        $this->schema  = $schema;
        $this->data    = $data;
        $this->options = $options;

    }

    public static function generator($object = [], $options = [], $data = [])
    {

        $generator = new self($object, $options, $data);
        return $generator->render();

    }

    public function render()
    {

        ob_start();
        foreach ($this->schema as $propertyName => $propertySchema)
        {
            $this->parser($propertySchema);
        }
        $fetched = ob_get_contents();
        ob_end_clean();
        return $fetched;

    }

    public function parser($schema)
    {

        if(TRUE === isset($schema['name']))
        {
            if(TRUE === isset($this->data[$schema['name']]))
            {
                $schema['value'] = $this->data[$schema['name']];
            }
            else
            {
                $schema['value'] = '';
            }
            if(TRUE === isset($this->options[$schema['name']]))
            {
                $schema['options'] = $this->options[$schema['name']];
            }
            else if(TRUE === isset($schema['options']))
            {
                $schema['options'] = $schema['options'];
            }
            else
            {
                $schema['options'] = [];
            }
        }
        extract($schema);

        $template = 'jsonSchemaForm/'.$schema['type'].'.phtml';
        require($template);

    }

}

/*        $json = <<<'php'
        {
            "element_name_select": {
                "type"    : "select",
                "label"   : "모듈명",
                "id"      : "ida",
                "name"    : "element_name_select",
                "options" : []
            },
            "element_name_text": {
                "type"    : "textarea",
                "label"   : "모듈명",
                "id"      : "idb",
                "name"    : "element_name_text"
            },
            "element_name_text2": {
                "type"    : "text",
                "label"   : "모듈명",
                "id"      : "idb",
                "name"    : "element_name_text"
            },
            "element_name_checkbox": {
                "type"    : "radio",
                "label"   : "주문모듈",
                "id"      : "idc",
                "name"    : "element_name_checkbox",
                "options" : [],
                "selectModule"    : [
                    "cart"
                ]
            },
            "element_name_radio": {
                "type"    : "radio",
                "label"   : "결제모듈",
                "id"      : "idd",
                "name"    : "element_name_radio",
                "options" : []
            },
            "element_name_text3": {
                "type"    : "text",
                "label"   : "모듈명",
                "id"      : "idf",
                "name"    : "element_name_text",
                "note"    : "모듈명을 입력하세요."
            }
        }
php;

        $options = [
            'element_name_select' => [
                [
                    "key" => "key1",
                    "value" => "value1"
                ],
                [
                    "key" => "key2",
                    "value" => "value2"
                ],
                [
                    "key" => "key3",
                    "value" => "value3"
                ],
                [
                    "key" => "key4",
                    "value" => "value4"
                ]
            ],
            'element_name_radio' => [
                [
                    "key" => "key1",
                    "value" => "value1"
                ],
                [
                    "key" => "key2",
                    "value" => "value2"
                ],
                [
                    "key" => "key3",
                    "value" => "value3"
                ],
                [
                    "key" => "key4",
                    "value" => "value4"
                ]
            ],
            'element_name_checkbox' => [
                [
                    "key" => "key1",
                    "value" => "value1"
                ],
                [
                    "key" => "key2",
                    "value" => "value2"
                ],
                [
                    "key" => "key3",
                    "value" => "value3"
                ],
                [
                    "key" => "key4",
                    "value" => "value4"
                ]
            ]
        ];
        $data = [
            'element_name_select'   => 'value3',
            'element_name_checkbox' => ['value3','value4'],
            'element_name_radio'    => 'value3',
            'element_name_text'     => 'aaddff'
        ];

        $html = \limepie\jsonSchemaForm::generator($json, $options, $data);*/