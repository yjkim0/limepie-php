<?php

namespace limepie;

/**

*/

class router
{

    private $pathinfo;
    private $routes;

    // segments는 url을 /로 자른것으로 forward되어도 변하지 않음
    private $segments            = [];
    private $arguments           = [];
    private $parameters          = [];

    private $basedir             = "app/module";
    private $prefix              = "";
    private $module              = "main";
    private $controller          = "index";
    private $action              = "index";
    private $matchRoute;
    private $systemVariables     = ["basedir","module","action","controller","prefix"];
    private $controllerDir       = "<basedir>/<module>";
    private $controllerNamespace = "<basedir>\<module>";
    private $actionSuffix        = "Action";
    private $controllerSuffix    = "Controller";
    private $notFound            = ['(.*)' => ["module" => "main", "controller" => "error", "action" => "index"]];
    private $errorInfo           = [];

    public function __construct($routes)
    {

        $this->routes = $routes;
        $this->pathinfo = \limepie\request::getPathinfo();
        $this->segments = \limepie\request::getSegments();

        // set request segment
        request::addData("segment", $this->segments);

    }

    public function setControllerDir($dir)
    {

        $this->controllerDir = $dir;

    }

    public function getControllerDir()
    {

        return $this->controllerDir;

    }

    public function setControllerNamespace($className)
    {

        $this->controllerNamespace = $className;

    }

    public function getControllerNamespace()
    {

        return $this->controllerNamespace;

    }

    public function getArguments()
    {

        return $this->arguments;

    }

    public function setRoutes($routes = [])
    {

        $this->routes = $routes;

    }

    public function add($key, $route = [])
    {

        $this->routes[$key] = $route;

    }

    public function setDefaultBaseDir($basedir)
    {

        $this->basedir = $basedir;

    }

    public function getBaseDir()
    {

        return $this->basedir;

    }

    public function setDefaultPrefix($prefix)
    {

        $this->prefix = $prefix;

    }

    public function getPrefix()
    {

        return $this->prefix;

    }

    public function setDefaultModule($module)
    {

        $this->module = $module;

    }

    public function getModule()
    {

        return $this->module;

    }

    public function setDefaultController($controller)
    {

        $this->controller = $controller;

    }

    public function getController()
    {

        return $this->controller;

    }

    public function setDefaultAction($action)
    {

        $this->action = $action;

    }

    public function getAction()
    {

        return $this->action;

    }

    public function setControllerSuffix($controller)
    {

        $this->controllerSuffix = $controller;

    }

    public function getControllerSuffix()
    {

        return $this->controllerSuffix;

    }

    public function setActionSuffix($action)
    {

        $this->actionSuffix   = $action;

    }

    public function getActionSuffix()
    {

        return $this->actionSuffix;

    }

    public function getPathinfo()
    {

        return $this->pathinfo;

    }

    public function getParameters()
    {

        return $this->parameters;

    }

    public function getParameter($key)
    {

        if (TRUE === isset($this->parameters[$key]))
        {
            return $this->parameters[$key];
        }
        return NULL;

    }

    public function getParam($key)
    {

        return $this->getParameter($key);

    }

    public function getSegments()
    {

        return $this->segments;

    }

    public function getSegment($key = NULL)
    {

        if (TRUE === isset($this->segments[$key]))
        {
            return $this->segments[$key];
        }
        return NULL;

    }

    private function getSystemVariables()
    {

        //var $ret, $key, $funcKey;
        $ret = [];

        foreach ($this->systemVariables as $key)
        {
            $funcKey = "get" . $key;
            if($tmp = $this->{$funcKey}())
            {
                $ret[$key] = $tmp;
            }
        }
        return $ret;

    }

    public function getMatchRoute()
    {

        return $this->matchRoute;

    }

    public function getDefaultVar($arr)
    {

        $ret = [];
        foreach($arr as $key => $value)
        {
            if($value)
            {
                $ret[$key] = $value;
            }
        }
        return $ret;

    }

    public function routing()
    {

        // var $def, $i, $parameters, $defaultVar, $rule, $key, $value, $m1, $_path, $c;

        if (!$this->routes || FALSE === is_array($this->routes) || !count($this->routes))
        {
            $this->routes["(?P<module>[^/]+)?"."(?:/(?P<controller>[^/]+))?"."(?:/(?P<action>[^/]+))?"."(?:/(?P<parameters>.*))?"] = [];
        }

        $this->parameters = [];
        $this->arguments  = [];
        $m1               = NULL;
        foreach ($this->routes as $rule => $defaultVar)
        {

            if (preg_match("#^" . $rule . "#", $this->pathinfo, $m1))
            {
                $parameters = $this->getSystemVariables();
                $defaultVar = $this->getDefaultVar($defaultVar);

                $this->parameters = $defaultVar + $parameters; // defaultVar 우선

                $_path  = [];
                if (TRUE === isset($m1["parameters"]))
                {
                    if (trim($m1["parameters"]) != "")
                    {
                        $_path = explode("/", rtrim($m1["parameters"], "/"));
                        $this->arguments = $_path;
                    }
                    unset($m1["parameters"]);
                }

                $c = count($_path) - 1;

                if($c > 0)
                {
                    for($i=0;$i<$c;$i+=2)
                    {
                        if (TRUE === isset($_path[$i])
                            && $_path[$i]
                            && FALSE === in_array($_path[$i], $this->systemVariables))
                        {
                            if (TRUE === isset($_path[$i +1]))
                            {
                                $this->parameters[$_path[$i]] = $_path[$i +1];
                            }
                            else
                            {
                                $this->parameters[$_path[$i]] = "";
                            }
                        }
                    }
                }

                foreach ($m1 as $key => $value)
                {
                    if (FALSE === is_numeric($key) && $value)
                    {
                        $this->parameters[$key] = $value;
                    }
                }
                $this->matchRoute = [$rule => $defaultVar];
                break;
            }
        }

        foreach($this->parameters as $key => &$parameter)
        {
            if(strpos($parameter,'<') !== FALSE) {
                foreach($this->parameters as $key2 => $parameter2)
                {
                    $parameter = str_replace('<'.$key2.'>', $parameter2, $parameter);
                }
            }
        }

        // set request parameter
        request::addData("parameter", $this->parameters);

    }

    public function notFound($route)
    {

        $this->notFound = ['(.*)' => $route];

    }

    public function getNotFound()
    {

        return $this->notFound;

    }

    public function setError($errorMessage, $errorCode, $arguments)
    {

        $this->errorInfo = [
            "errorMessage" => $errorMessage,
            "errorCode"    => $errorCode,
            "arguments"    => $arguments
        ];

    }

    public function getError()
    {

        return $this->errorInfo;

    }

}