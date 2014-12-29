<?php

namespace limepie\router;

class exception extends \exception
{
    private $data;

    public function __construct($message, $engmessage, $data = NULL)
    {
        parent::__construct($message);

        if (!is_null($data))
        {
            $this->data = $data;
        }
    }

    public function getData()
    {
        return $this->data;
    }
}
