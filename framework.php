<?php

namespace limepie;

class framework
{

    const FOLDER_DOES_NOT_EXISTS = 'folder';
    const FILE_DOES_NOT_EXISTS   = 'file';
    const CLASS_DOES_NOT_EXISTS  = 'class';
    const METHOD_DOES_NOT_EXISTS = 'method';

    private static $instance = NULL;
    public $router;
    public $prevRouter;
    public $request;

    public function __construct() {}
    public function __destruct() {}

    public static function getInstance()
    {

        if (!self::$instance)
        {
            self::$instance = new self;
        }
        return self::$instance;

    }

    public function setRouter(\limepie\router $router)
    {

        $router->routing();
        $this->router = $router;

    }

    public function setRequest($request)
    {

        $this->request = $request;

    }

    public function getRequest()
    {

        return $this->request;

    }

    public function getRouter()
    {

        return $this->router;

    }

    public function setPrevRouter(\limepie\router $router)
    {

        $this->prevRouter = $router;

    }

    public function getPrevRouter()
    {

        return $this->prevRouter;

    }

    private function run($arguments = [])
    {

        // var $module, $controller, $action, $basedir, $prefix;

        $module          = $this->getRouter()->getParameter("module");
        $controller      = $this->getRouter()->getParameter("controller");
        $action          = $this->getRouter()->getParameter("action");
        $basedir         = $this->getRouter()->getParameter("basedir");
        $prefix          = $this->getRouter()->getParameter("prefix");

        $namespaceName = $this->getRouter()->getControllerNamespace();
        foreach($this->getRouter()->getParameters()as $key => $parameter)
        {
            $namespaceName = str_replace(['<'.$key.'>','/'], [$parameter,'\\'], $namespaceName);
        }

        $className       = $controller.$this->getRouter()->getControllerSuffix();
        $actionName      = $action.$this->getRouter()->getActionSuffix();

        $baseFolderName = $this->getRouter()->getControllerDir();
        foreach($this->getRouter()->getParameters()as $key => $parameter)
        {
            $baseFolderName = str_replace('<'.$key.'>', $parameter, $baseFolderName);
        }

        $fileName        = $baseFolderName."/".$className.".php";
        $folderName      = dirname($fileName);
        $callClassName   = $namespaceName."\\".$className;

        if(!$arguments)
        {
            $arguments   = $this->getRouter()->getArguments();
        }

        $requireInfo     = [
            'namespace'         => $namespaceName
            , 'folder'          => $folderName
            , 'file'            => $fileName
            , 'class'           => $className
            , 'method'          => $actionName
        ];

        $fileName        = stream_resolve_include_path($fileName);
        if (TRUE === file_exists($fileName) && TRUE === is_readable($fileName))
        {

            if (FALSE === in_array($fileName, get_included_files()))
            {
                require $fileName;
            }

            if (FALSE === class_exists($callClassName, FALSE))
            {
                return $this->forwardNotFound($this->getRouter()->getNotFound(), "CLASS_DOES_NOT_EXISTS", self::CLASS_DOES_NOT_EXISTS, $requireInfo);
            }

            $requestMethod = "get";
            if ($tmp = getenv("REQUEST_METHOD"))
            {
                $requestMethod = strtolower($tmp);
            }

            $instance;
            $instance = new $callClassName();

            $arrActionName = [$requestMethod.$actionName, $actionName];
            foreach($arrActionName as $action) {
                if(TRUE === method_exists($instance, $action) && TRUE === is_callable([$instance, $action]) )
                {
                    return call_user_func_array([$instance, $action], $arguments);
                }
            }
            return $this->forwardNotFound($this->getRouter()->getNotFound(), "METHOD_DOES_NOT_EXISTS", self::METHOD_DOES_NOT_EXISTS, $requireInfo);

        }
        else
        {
            $folderName = stream_resolve_include_path($folderName);
            if(FALSE === is_dir($folderName))
            {
                return $this->forwardNotFound($this->getRouter()->getNotFound(), "FOLDER_DOES_NOT_EXISTS", self::FOLDER_DOES_NOT_EXISTS, $requireInfo);
            }
            else
            {
                return $this->forwardNotFound($this->getRouter()->getNotFound(), "FILE_DOES_NOT_EXISTS", self::FILE_DOES_NOT_EXISTS, $requireInfo);
            }
        }

    }

    private function forwardNotFound($routes = [], $errorMessage, $errorCode, $errorData)
    {

        //var $router, $framework, $newRouter;

        $this->setPrevRouter(clone $this->getRouter());
        $this->getRouter()->setRoutes($routes);
        $framework  = self::getInstance();
        $framework->setRouter($this->getRouter());
        $newRouter  = $framework->getRouter();
        // $newRouter->setError($errorMessage, $errorCode, $errorData);

        if($this->getPrevRouter()->getMatchRoute() == $newRouter->getMatchRoute())
        {
            throw new \limepie\router\exception(("error 404route."), "error 404route", $arguments);
        }
        return $framework->dispatch([$errorMessage, $errorCode, $errorData]);

    }

    public function forward($config)
    {

        $arguments;
        if(TRUE === isset($config["arguments"]))
        {
            $arguments = $config["arguments"];
        }
        else
        {
            $arguments = [];
        }
        return $this->_forward($config["route"], $arguments);

    }

    private function _forward($routes = [], $arguments = [])
    {

        //var $framework, $newRouter;

        $this->setPrevRouter(clone $this->getRouter());
        $this->getRouter()->setRoutes($routes);
        $framework  = self::getInstance();
        $framework->setRouter($this->getRouter());
        $newRouter  = $framework->getRouter();

        if($this->getPrevRouter()->getMatchRoute() == $newRouter->getMatchRoute())
        {
            throw new \limepie\router\exception(("error forward route."), "error foward route", $arguments);
        }
        return $framework->dispatch($arguments);

    }

    public function dispatch($arguments = [])
    {

        return $this->run($arguments);

    }
}
