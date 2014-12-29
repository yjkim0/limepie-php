<?php

namespace limepie;

class Controller
{

    public $framework;
    private $router;
    protected $segments; // number
    protected $parameters;

    public function __construct()
    {

        $this->framework  = framework::getInstance();
        $this->setRouter($this->framework->getRouter());

        $this->segments   = $this->getRouter()->getSegments();
        $this->parameters = $this->getRouter()->getParameters();

    }

    protected function setRouter($router)
    {

        $this->router = $router;

    }

    protected function getRouter()
    {

        return $this->router;

    }

    protected function getSegment($key1 = FALSE, $def = "")
    {

        return $this->getRouter()->getSegment($key1);

    }

    protected function getParameter($key1 = FALSE, $end = FALSE)
    {

        return $this->getRouter()->getParameter($key1);

    }

    protected function getUri()
    {

        return $this->getRouter()->pathinfo();

    }

    protected function getModule()
    {

        return $this->getRouter()->getModule();

    }

    protected function getController()
    {

        return $this->getRouter()->getController();

    }

    protected function getAction()
    {

        return $this->getRouter()->getAction();

    }

    protected function getErrorController()
    {

        return $this->getRouter()->getErrorController();

    }

    public function forward($routeValue, $args = [])
    {

        return $this->getRouter()->forward([
            '(.*)' => $routeValue
        ], $args);

    }

}
