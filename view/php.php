<?php


namespace limepie\View;

class php
{

    public $tpl_; // = [];
    public $var_; // = [];
    public $skin;
    public $tpl_path;

    public function __construct()
    {

        $this->tpl_ = [];
        $this->var_ = [];

    }

    public function assign($arg, $path = FALSE)
    {

        if (TRUE === is_array($arg))
        {
            $this->var_ = array_merge($this->var_, $arg);
        }
        else
        {
            $this->var_[$arg] = $path;
        }

    }

    public function define($arg, $path = FALSE)
    {

        if ($path)
        {
            $this->_define($arg, $path);
        }
        else
        {
            foreach ($arg as $fid => $path2)
            {
                $this->_define($fid, $path2);
            }
        }

    }

    public function _define($fid, $path)
    {

        $this->tpl_[$fid] = $path;

    }

    public function show($fid, $print = FALSE)
    {

        if (TRUE === $print)
        {
            $this->render($fid);
        }
        else
        {
            return $this->fetched($fid);
        }

    }

    public function fetched($fid)
    {

        ob_start();
        $this->render($fid);
        $fetched = ob_get_contents();
        ob_end_clean();

        return $fetched;

    }

    public function render($fid)
    {

        $tpl_path       = $this->tpl_path($fid);

        if (FALSE === is_file($tpl_path))
        {
            throw new \limepie\view\exception("템플릿 파일이 없음 : " . $tpl_path);
        }

        $this->_include_tpl($tpl_path, $fid); //, scope);

    }

    public function _include_tpl($cpl, $tpl)
    {//, TPL_SCP)

        extract($this->var_);
        require $cpl;

    }

    public function tpl_path($fid)
    {

        $path = "";

        if (TRUE == isset($this->tpl_[$fid]))
        {
            $path = $this->tpl_[$fid];
        }
        else
        {
            throw new peanut\exception($fid . "이(가) 정의되어있지 않음");
        }
        if (substr($path, 0, 1) == "/")
        {
            return $path;
        }
        else
        {
            $skinFolder = trim($this->skin, "/");

            if ($skinFolder)
            {
                $addFolder = $skinFolder . "/";
            }
            else
            {
                $addFolder = "";
            }
            $front          = \limepie\framework::getInstance();
            $router         = $front->getRouter();

            $module         = $router->getParameter("module");
            $controller     = $router->getParameter("controller");
            $action         = $router->getParameter("action");
            $basedir        = $router->getParameter("basedir");
            $prefix         = $router->getParameter("prefix");

            $path = strtr($path, [
                "<basedir>"         => $basedir
                , "<prefix>"        => $prefix
                , "<module>"        => $module
                , "<controller>"    => $controller
                , "<action>"        => $action
            ]);

            $this->tpl_[$fid] = stream_resolve_include_path($addFolder . $path);
            return $this->tpl_[$fid];

        }

    }

}
